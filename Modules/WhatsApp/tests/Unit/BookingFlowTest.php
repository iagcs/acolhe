<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\Session\Actions\StoreSessionAction;
use Modules\Session\Models\Session;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Flows\BookingFlow;
use Modules\WhatsApp\Models\WhatsAppConversation;
use Modules\WhatsApp\Services\SlotFinderService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeBookingConversation(array $attrs = []): WhatsAppConversation
{
    $psychologist = \Database\Factories\UserFactory::new()->create([
        'session_duration' => 50,
        'session_interval' => 10,
    ]);
    $patient = \Database\Factories\PatientFactory::new()->create(['psychologist_id' => $psychologist->id]);

    return WhatsAppConversation::create(array_merge([
        'phone'           => '5511999999999',
        'psychologist_id' => $psychologist->id,
        'patient_id'      => $patient->id,
        'state'           => 'idle',
        'context'         => [],
        'last_message_at' => now(),
        'expires_at'      => now()->addHours(24),
    ], $attrs));
}

function makeMsg(string $text = '', ?string $buttonId = null, ?string $listRowId = null): IncomingMessageDTO
{
    return new IncomingMessageDTO(
        phone: '5511999999999',
        psychologistPhone: '5511988888888',
        text: $text,
        buttonId: $buttonId,
        listRowId: $listRowId,
        messageType: $listRowId ? 'list_reply' : ($buttonId ? 'button_reply' : 'text'),
        externalId: null,
    );
}

describe('BookingFlow', function () {

    it('triggers on booking keywords', function () {
        $slotFinder = Mockery::mock(SlotFinderService::class);
        $store      = Mockery::mock(StoreSessionAction::class);
        $flow       = new BookingFlow($slotFinder, $store);

        $conv = makeBookingConversation(['state' => 'idle']);

        foreach (['agendar', 'Agendar', 'marcar sessão', 'horario', 'consulta'] as $keyword) {
            expect($flow->canHandle($conv, makeMsg($keyword)))->toBeTrue();
        }
    });

    it('does not trigger on unrelated text', function () {
        $slotFinder = Mockery::mock(SlotFinderService::class);
        $store      = Mockery::mock(StoreSessionAction::class);
        $flow       = new BookingFlow($slotFinder, $store);

        $conv = makeBookingConversation(['state' => 'idle']);
        expect($flow->canHandle($conv, makeMsg('Oi tudo bem')))->toBeFalse();
    });

    it('returns text message when no slots are available', function () {
        $slotFinder = Mockery::mock(SlotFinderService::class);
        $slotFinder->shouldReceive('findAvailable')->andReturn(collect());
        $store = Mockery::mock(StoreSessionAction::class);

        $flow  = new BookingFlow($slotFinder, $store);
        $conv  = makeBookingConversation(['state' => 'idle']);
        $reply = $flow->handle($conv, makeMsg('agendar'));

        expect($reply->replyType)->toBe('text');
        expect($reply->message)->toContain('Não há horários');
        expect($reply->nextState)->toBe('idle');
    });

    it('shows a list message when slots are available', function () {
        $slot = CarbonImmutable::now()->addDay()->setTime(10, 0);

        $slotFinder = Mockery::mock(SlotFinderService::class);
        $slotFinder->shouldReceive('findAvailable')->andReturn(collect([$slot]));
        $store = Mockery::mock(StoreSessionAction::class);

        $flow  = new BookingFlow($slotFinder, $store);
        $conv  = makeBookingConversation(['state' => 'idle']);
        $reply = $flow->handle($conv, makeMsg('agendar'));

        expect($reply->replyType)->toBe('list');
        expect($reply->nextState)->toBe('booking_slots');
        expect($reply->sections)->not->toBeEmpty();
        expect($reply->sections[0]['rows'][0]['id'])->toStartWith('slot_');
    });

    it('shows confirmation buttons after slot selection', function () {
        $slot = CarbonImmutable::now()->addDays(2)->setTime(14, 0);

        $slotFinder = Mockery::mock(SlotFinderService::class);
        $store      = Mockery::mock(StoreSessionAction::class);
        $flow       = new BookingFlow($slotFinder, $store);

        $conv  = makeBookingConversation(['state' => 'booking_slots']);
        $msg   = makeMsg('', null, 'slot_' . $slot->format('Y-m-d\TH:i'));
        $reply = $flow->handle($conv, $msg);

        expect($reply->replyType)->toBe('buttons');
        expect($reply->nextState)->toBe('booking_confirm');
        expect($reply->contextPatch['pending_slot'])->not->toBeNull();
    });

    it('creates session on booking confirmation', function () {
        $slotFinder = Mockery::mock(SlotFinderService::class);
        $store      = app(StoreSessionAction::class);

        $flow = new BookingFlow($slotFinder, $store);
        $conv = makeBookingConversation([
            'state'   => 'booking_confirm',
            'context' => ['pending_slot' => Carbon::now()->addDays(2)->setTime(14, 0)->format('Y-m-d H:i:s')],
        ]);

        $reply = $flow->handle($conv, makeMsg('', 'book_yes'));

        expect($reply->nextState)->toBe('idle');
        expect($reply->message)->toContain('agendada');
        expect(Session::where('patient_id', $conv->patient_id)->exists())->toBeTrue();
    });

    it('cancels booking on negative confirmation', function () {
        $slotFinder = Mockery::mock(SlotFinderService::class);
        $store      = Mockery::mock(StoreSessionAction::class);
        $store->shouldNotReceive('execute');

        $flow  = new BookingFlow($slotFinder, $store);
        $conv  = makeBookingConversation([
            'state'   => 'booking_confirm',
            'context' => ['pending_slot' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s')],
        ]);
        $reply = $flow->handle($conv, makeMsg('', 'book_no'));

        expect($reply->nextState)->toBe('idle');
        expect($reply->message)->toContain('cancelado');
    });
});
