<?php

use Modules\Session\Actions\UpdateSessionAction;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Models\Session;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Flows\ConfirmationFlow;
use Modules\WhatsApp\Models\WhatsAppConversation;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeConfirmConversation(array $context = []): WhatsAppConversation
{
    $psychologist = \Database\Factories\UserFactory::new()->create();
    $patient      = \Database\Factories\PatientFactory::new()->create(['psychologist_id' => $psychologist->id]);

    return WhatsAppConversation::create([
        'phone'           => '5511999999999',
        'psychologist_id' => $psychologist->id,
        'patient_id'      => $patient->id,
        'state'           => 'confirm_session',
        'context'         => $context,
        'last_message_at' => now(),
        'expires_at'      => now()->addHours(24),
    ]);
}

function makeConfirmMsg(?string $buttonId = null, string $text = ''): IncomingMessageDTO
{
    return new IncomingMessageDTO(
        phone: '5511999999999',
        psychologistPhone: '5511988888888',
        text: $text,
        buttonId: $buttonId,
        listRowId: null,
        messageType: $buttonId ? 'button_reply' : 'text',
        externalId: null,
    );
}

describe('ConfirmationFlow', function () {

    it('only handles confirm_session state', function () {
        $flow = new ConfirmationFlow(app(UpdateSessionAction::class));
        $conv = makeConfirmConversation();
        $conv->state = 'idle';

        expect($flow->canHandle($conv, makeConfirmMsg('btn_confirm')))->toBeFalse();

        $conv->state = 'confirm_session';
        expect($flow->canHandle($conv, makeConfirmMsg('btn_confirm')))->toBeTrue();
    });

    it('confirms session when confirm button is pressed', function () {
        $psychologist = \Database\Factories\UserFactory::new()->create();
        $patient      = \Database\Factories\PatientFactory::new()->create(['psychologist_id' => $psychologist->id]);

        $session = Session::create([
            'psychologist_id' => $psychologist->id,
            'patient_id'      => $patient->id,
            'starts_at'       => now()->addDay()->setTime(14, 0),
            'ends_at'         => now()->addDay()->setTime(14, 50),
            'status'          => SessionStatus::Scheduled,
            'type'            => 'in_person',
        ]);

        $conv = WhatsAppConversation::create([
            'phone'           => '5511999999999',
            'psychologist_id' => $psychologist->id,
            'patient_id'      => $patient->id,
            'state'           => 'confirm_session',
            'context'         => ['pending_confirmation_session_id' => $session->id],
            'last_message_at' => now(),
            'expires_at'      => now()->addHours(24),
        ]);

        $flow  = new ConfirmationFlow(app(UpdateSessionAction::class));
        $reply = $flow->handle($conv, makeConfirmMsg('btn_confirm'));

        expect($reply->nextState)->toBe('idle');
        expect($reply->message)->toContain('Confirmada');
        expect($session->fresh()->status)->toBe(SessionStatus::Confirmed);
    });

    it('transitions to reschedule_slots when reschedule is selected', function () {
        $flow  = new ConfirmationFlow(app(UpdateSessionAction::class));
        $conv  = makeConfirmConversation();
        $reply = $flow->handle($conv, makeConfirmMsg('btn_reschedule'));

        expect($reply->nextState)->toBe('reschedule_slots');
    });
});
