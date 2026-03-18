<?php

namespace Modules\WhatsApp\Flows;

use Modules\Patient\Actions\StorePatientAction;
use Modules\Patient\DTOs\PatientData;
use Modules\WhatsApp\Contracts\ConversationHandlerInterface;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;

/**
 * Handles new patient registration via WhatsApp.
 *
 * State flow:
 *   idle (unrecognised phone)  → onboarding_name
 *   onboarding_name            → onboarding_confirm  (stores collected name)
 *   onboarding_confirm         → idle or booking_slots (patient created)
 */
class OnboardingFlow implements ConversationHandlerInterface
{
    public function __construct(
        private readonly StorePatientAction $storePatientAction,
    ) {}

    public function canHandle(WhatsAppConversation $conversation, IncomingMessageDTO $message): bool
    {
        // Handle new visitors or ongoing onboarding states
        if (in_array($conversation->state, ['onboarding_name', 'onboarding_confirm'])) {
            return true;
        }

        // New phone with no linked patient
        if ($conversation->state === 'idle' && $conversation->patient_id === null) {
            return true;
        }

        return false;
    }

    public function handle(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        return match ($conversation->state) {
            'idle'               => $this->askName(),
            'onboarding_name'    => $this->confirmName($message->text),
            'onboarding_confirm' => $this->createPatient($conversation, $message),
            default              => $this->askName(),
        };
    }

    // ── Steps ──────────────────────────────────────────────────────────────

    private function askName(): ConversationReply
    {
        return ConversationReply::text(
            message: "Olá! 👋 Não te encontrei no sistema ainda.\n\nQual é o seu *nome completo*?",
            nextState: 'onboarding_name',
        );
    }

    private function confirmName(string $name): ConversationReply
    {
        $name = trim($name);

        if (mb_strlen($name) < 3 || ! str_contains($name, ' ')) {
            return ConversationReply::text(
                message: "Por favor, informe seu *nome completo* (nome e sobrenome).",
                nextState: 'onboarding_name',
            );
        }

        return ConversationReply::buttons(
            message: "Confirmar seu cadastro como *{$name}*?",
            buttons: [
                ['id' => 'onboard_yes', 'text' => '✅ Sim, confirmar'],
                ['id' => 'onboard_no',  'text' => '✏️ Corrigir nome'],
            ],
            nextState: 'onboarding_confirm',
            contextPatch: ['pending_name' => $name],
        );
    }

    private function createPatient(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        $input = $message->effectiveInput();

        // Patient wants to correct the name
        if ($input === 'onboard_no' || str_contains($input, 'corrigir')) {
            return $this->askName();
        }

        $name = $conversation->getContext('pending_name');

        if (! $name) {
            return $this->askName();
        }

        $psychologist = $conversation->psychologist;

        $patient = $this->storePatientAction->execute($psychologist, new PatientData(
            name: $name,
            phone: $conversation->phone,
            email: null,
            birth_date: null,
            notes: null,
        ));

        return ConversationReply::text(
            message: "✅ Pronto, *{$patient->name}*! Você já está cadastrado(a).\n\nDigite *agendar* para marcar uma sessão ou *ajuda* para ver as opções disponíveis.",
            nextState: 'idle',
            contextPatch: ['patient_id' => $patient->id],
        );
    }
}
