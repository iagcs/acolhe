<?php

use Modules\Patient\Actions\StorePatientAction;
use Modules\Patient\Models\Patient;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Flows\OnboardingFlow;
use Modules\WhatsApp\Models\WhatsAppConversation;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeConversation(array $attrs = []): WhatsAppConversation
{
    $psychologist = \Database\Factories\UserFactory::new()->create();

    return WhatsAppConversation::create(array_merge([
        'phone'           => '5511999999999',
        'psychologist_id' => $psychologist->id,
        'state'           => 'idle',
        'context'         => [],
        'last_message_at' => now(),
        'expires_at'      => now()->addHours(24),
    ], $attrs));
}

function makeMessage(string $text = '', ?string $buttonId = null, ?string $listRowId = null): IncomingMessageDTO
{
    return new IncomingMessageDTO(
        phone: '5511999999999',
        psychologistPhone: '5511988888888',
        text: $text,
        buttonId: $buttonId,
        listRowId: $listRowId,
        messageType: $buttonId ? 'button_reply' : 'text',
        externalId: 'msg_test',
    );
}

describe('OnboardingFlow', function () {

    beforeEach(function () {
        $this->action = app(StorePatientAction::class);
        $this->flow   = new OnboardingFlow($this->action);
    });

    it('triggers for idle conversation with no patient linked', function () {
        $conv = makeConversation(['state' => 'idle', 'patient_id' => null]);
        $msg  = makeMessage('Oi');

        expect($this->flow->canHandle($conv, $msg))->toBeTrue();
    });

    it('does not trigger for idle conversation with existing patient', function () {
        $psychologist = \Database\Factories\UserFactory::new()->create();
        $patient      = \Database\Factories\PatientFactory::new()->create(['psychologist_id' => $psychologist->id]);
        $conv = makeConversation(['state' => 'idle', 'patient_id' => $patient->id]);
        $msg  = makeMessage('Oi');

        expect($this->flow->canHandle($conv, $msg))->toBeFalse();
    });

    it('asks for name in idle state', function () {
        $conv  = makeConversation(['state' => 'idle']);
        $msg   = makeMessage('Oi');
        $reply = $this->flow->handle($conv, $msg);

        expect($reply->nextState)->toBe('onboarding_name');
        expect($reply->replyType)->toBe('text');
        expect($reply->message)->toContain('nome completo');
    });

    it('rejects name without surname', function () {
        $conv  = makeConversation(['state' => 'onboarding_name']);
        $msg   = makeMessage('Ana');
        $reply = $this->flow->handle($conv, $msg);

        expect($reply->nextState)->toBe('onboarding_name');
    });

    it('shows confirm buttons after full name is provided', function () {
        $conv  = makeConversation(['state' => 'onboarding_name']);
        $msg   = makeMessage('Ana Clara Souza');
        $reply = $this->flow->handle($conv, $msg);

        expect($reply->replyType)->toBe('buttons');
        expect($reply->nextState)->toBe('onboarding_confirm');
        expect($reply->contextPatch['pending_name'])->toBe('Ana Clara Souza');
        expect($reply->buttons)->toHaveCount(2);
    });

    it('creates patient and transitions to idle on confirmation', function () {
        $conv  = makeConversation(['state' => 'onboarding_confirm', 'context' => ['pending_name' => 'Ana Clara Souza']]);
        $msg   = makeMessage('', 'onboard_yes');
        $reply = $this->flow->handle($conv, $msg);

        expect($reply->nextState)->toBe('idle');
        expect($reply->contextPatch['patient_id'])->not->toBeNull();
        expect(Patient::where('name', 'Ana Clara Souza')->exists())->toBeTrue();
    });

    it('returns to name collection when user wants to correct name', function () {
        $conv  = makeConversation(['state' => 'onboarding_confirm', 'context' => ['pending_name' => 'Wrong Name']]);
        $msg   = makeMessage('', 'onboard_no');
        $reply = $this->flow->handle($conv, $msg);

        expect($reply->nextState)->toBe('onboarding_name');
    });
});
