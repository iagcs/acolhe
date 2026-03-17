<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Agenda\Models\Availability;
use Modules\Agenda\Models\Recurrence;
use Modules\Auth\Models\User;
use Modules\Billing\Models\BillingSettings;
use Modules\Billing\Models\Payment;
use Modules\Patient\Models\Consent;
use Modules\Patient\Models\Patient;
use Modules\Reminder\Models\Reminder;
use Modules\RiskScore\Models\RiskScoreEvent;
use Modules\Session\Models\Session;
use Modules\WaitingList\Models\WaitingListEntry;

class TestPsychologistSeeder extends Seeder
{
    /**
     * Seed a complete testing environment for a psychologist with:
     * - 1 psychologist (Dra. Ana Clara Mendes)
     * - 8 patients (varied profiles)
     * - Weekly availability (Mon-Fri)
     * - Recurrences for recurring patients
     * - Sessions across past, present, and future
     * - Reminders for upcoming sessions
     * - Payments and receipts
     * - Billing settings
     * - Patient consents
     * - Risk score events
     * - 1 patient on waiting list
     */
    public function run(): void
    {
        // ─── Psychologist ───────────────────────────────────────────────
        $psychologist = User::create([
            'name' => 'Dra. Ana Clara Mendes',
            'email' => 'ana.mendes@psiagenda.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'phone' => '11987654321',
            'crp' => '06/123456',
            'timezone' => 'America/Sao_Paulo',
            'session_duration' => 50,
            'session_interval' => 10,
            'session_price' => 250.00,
            'therapeutic_approach' => 'tcc',
            'plan' => 'professional',
            'plan_expires_at' => now()->addYear(),
            'fiscal_data' => [
                'cpf' => '123.456.789-00',
                'address' => 'Rua Augusta, 1200, Sala 305 - Consolação, São Paulo/SP',
                'cnpj' => null,
            ],
            'ai_settings' => [
                'approach' => 'tcc',
                'enabled' => true,
                'max_questions' => 5,
                'custom_instructions' => 'Focar em identificar distorções cognitivas e padrões comportamentais.',
                'sample_questions' => [
                    'Como você tem se sentido nas últimas semanas?',
                    'Existem pensamentos que têm te incomodado com frequência?',
                    'Como está seu sono e apetite?',
                    'Você tem conseguido realizar suas atividades do dia a dia?',
                    'Existe alguma situação específica que gostaria de abordar?',
                ],
            ],
            'settings' => [
                'reminder_24h' => true,
                'reminder_2h' => true,
                'auto_confirm' => false,
                'whatsapp_notifications' => true,
                'receipt_auto_send' => true,
                'cancellation_policy_hours' => 24,
            ],
            'slug' => 'dra-ana-clara-mendes',
        ]);

        // ─── Billing Settings ───────────────────────────────────────────
        BillingSettings::create([
            'psychologist_id' => $psychologist->id,
            'billing_mode' => 'post',
            'default_method' => 'pix',
            'auto_receipt' => true,
            'reminder_overdue' => true,
            'pix_key' => '123.456.789-00',
        ]);

        // ─── Availability (Mon-Fri, 08:00-18:00) ──────────────────────
        foreach (range(1, 5) as $dayOfWeek) {
            Availability::create([
                'psychologist_id' => $psychologist->id,
                'day_of_week' => $dayOfWeek,
                'start_time' => '08:00',
                'end_time' => '18:00',
                'is_active' => true,
            ]);
        }

        // ─── Patients ──────────────────────────────────────────────────
        $patients = $this->createPatients($psychologist);

        // ─── Recurrences ────────────────────────────────────────────────
        $recurrences = $this->createRecurrences($psychologist, $patients);

        // ─── Sessions ───────────────────────────────────────────────────
        $this->createSessions($psychologist, $patients, $recurrences);

        // ─── Waiting List ───────────────────────────────────────────────
        $this->createWaitingListEntries($psychologist, $patients);

        // ─── Consents ───────────────────────────────────────────────────
        $this->createConsents($patients);

        $this->command->info('✅ Psicóloga de teste criada: ana.mendes@psiagenda.test / password');
        $this->command->info("   ID: {$psychologist->id}");
        $this->command->info('   Pacientes: '.count($patients));
    }

    /**
     * Create 8 patients with varied profiles.
     */
    private function createPatients(User $psychologist): array
    {
        $patientsData = [
            [
                'name' => 'Maria Silva Santos',
                'phone' => '11998765432',
                'email' => 'maria.silva@email.com',
                'birth_date' => '1990-05-15',
                'notes' => 'Ansiedade generalizada. Encaminhada pelo psiquiatra Dr. Roberto. Usa Sertralina 50mg.',
                'is_active' => true,
                'risk_score' => 0,
                'risk_level' => 'low',
                'ai_screening_status' => 'completed',
                'ai_screening_summary' => 'Paciente relata ansiedade moderada com impacto no sono. Apresenta distorções cognitivas: catastrofização e leitura mental. Motivada para o tratamento.',
                'metadata' => ['occupation' => 'Engenheira de Software', 'health_plan' => 'Unimed'],
            ],
            [
                'name' => 'João Pedro Oliveira',
                'phone' => '11987652345',
                'email' => 'joao.pedro@email.com',
                'birth_date' => '1985-11-20',
                'notes' => 'Depressão recorrente. Terceiro episódio. Resistência inicial ao tratamento mas evoluiu bem.',
                'is_active' => true,
                'risk_score' => 35,
                'risk_level' => 'medium',
                'session_price_override' => 200.00,
                'ai_screening_status' => 'completed',
                'ai_screening_summary' => 'Relata humor deprimido persistente, anedonia e dificuldade de concentração. Histórico de 2 episódios prévios. Sem ideação suicida ativa.',
                'metadata' => ['occupation' => 'Professor Universitário', 'health_plan' => 'SulAmérica'],
            ],
            [
                'name' => 'Carla Beatriz Ferreira',
                'phone' => '11976543456',
                'email' => 'carla.ferreira@email.com',
                'birth_date' => '1998-03-08',
                'notes' => 'Síndrome do pânico. Boa adesão ao tratamento. Progresso significativo nas últimas 8 sessões.',
                'is_active' => true,
                'risk_score' => 10,
                'risk_level' => 'low',
                'ai_screening_status' => 'completed',
                'ai_screening_summary' => 'Relata crises de pânico com frequência semanal. Evitação de situações sociais. Motivada para exposição gradual.',
                'metadata' => ['occupation' => 'Designer Gráfica', 'health_plan' => null],
            ],
            [
                'name' => 'Ricardo Almeida Costa',
                'phone' => '11965432567',
                'email' => 'ricardo.costa@email.com',
                'birth_date' => '1978-07-22',
                'notes' => 'Problemas de relacionamento conjugal. Vem sozinho, esposa não aceita terapia de casal.',
                'is_active' => true,
                'risk_score' => 15,
                'risk_level' => 'low',
                'ai_screening_status' => 'none',
                'metadata' => ['occupation' => 'Advogado', 'health_plan' => 'Bradesco Saúde'],
            ],
            [
                'name' => 'Fernanda Lima Souza',
                'phone' => '11954323678',
                'email' => 'fernanda.lima@email.com',
                'birth_date' => '2002-01-30',
                'notes' => 'TDAH diagnosticado na adolescência. Busca estratégias para organização e foco na faculdade.',
                'is_active' => true,
                'risk_score' => 5,
                'risk_level' => 'low',
                'ai_screening_status' => 'in_progress',
                'metadata' => ['occupation' => 'Estudante de Medicina', 'health_plan' => 'Amil'],
            ],
            [
                'name' => 'Carlos Eduardo Nunes',
                'phone' => '11943214789',
                'email' => 'carlos.nunes@email.com',
                'birth_date' => '1970-09-12',
                'notes' => 'Luto pela perda da esposa há 6 meses. Apresentou melhora progressiva. Alta terapêutica em discussão.',
                'is_active' => true,
                'risk_score' => 75,
                'risk_level' => 'high',
                'ai_screening_status' => 'declined',
                'metadata' => ['occupation' => 'Empresário', 'health_plan' => null],
            ],
            [
                'name' => 'Juliana Martins Rocha',
                'phone' => '11932105890',
                'email' => 'juliana.rocha@email.com',
                'birth_date' => '1995-12-05',
                'notes' => 'Paciente inativa desde fev/2026. Abandonou tratamento sem aviso. Tentativa de contato sem retorno.',
                'is_active' => false,
                'risk_score' => 50,
                'risk_level' => 'medium',
                'ai_screening_status' => 'completed',
                'ai_screening_summary' => 'Relata TOC com rituais de verificação. Impacto moderado na rotina diária.',
                'metadata' => ['occupation' => 'Contadora', 'health_plan' => 'Unimed'],
            ],
            [
                'name' => 'Lucas Gabriel Araujo',
                'phone' => '11921096901',
                'email' => null,
                'birth_date' => '2005-06-18',
                'notes' => 'Paciente adolescente. Fobia social. Atendimento com consentimento dos pais. Sem email.',
                'is_active' => true,
                'risk_score' => 20,
                'risk_level' => 'low',
                'billing_enabled' => false,
                'ai_screening_status' => 'invited',
                'metadata' => [
                    'occupation' => 'Estudante',
                    'health_plan' => 'Amil',
                    'guardian_name' => 'Marcos Araujo',
                    'guardian_phone' => '11912345678',
                ],
            ],
        ];

        $patients = [];
        foreach ($patientsData as $data) {
            $data['psychologist_id'] = $psychologist->id;
            $patients[] = Patient::create($data);
        }

        // 7th patient referred by 1st patient (Maria referred Juliana)
        $patients[6]->update(['referred_by' => $patients[0]->id]);

        return $patients;
    }

    /**
     * Create weekly recurrences for the 5 most active patients.
     */
    private function createRecurrences(User $psychologist, array $patients): array
    {
        $recurrenceData = [
            // Maria: Mondays 09:00-09:50
            [
                'patient' => $patients[0],
                'day_of_week' => 1,
                'start_time' => '09:00',
                'end_time' => '09:50',
            ],
            // João: Mondays 10:00-10:50
            [
                'patient' => $patients[1],
                'day_of_week' => 1,
                'start_time' => '10:00',
                'end_time' => '10:50',
            ],
            // Carla: Wednesdays 14:00-14:50
            [
                'patient' => $patients[2],
                'day_of_week' => 3,
                'start_time' => '14:00',
                'end_time' => '14:50',
            ],
            // Ricardo: Thursdays 11:00-11:50
            [
                'patient' => $patients[3],
                'day_of_week' => 4,
                'start_time' => '11:00',
                'end_time' => '11:50',
            ],
            // Fernanda: Fridays 15:00-15:50
            [
                'patient' => $patients[4],
                'day_of_week' => 5,
                'start_time' => '15:00',
                'end_time' => '15:50',
            ],
        ];

        $recurrences = [];
        foreach ($recurrenceData as $data) {
            $recurrences[] = Recurrence::create([
                'psychologist_id' => $psychologist->id,
                'patient_id' => $data['patient']->id,
                'day_of_week' => $data['day_of_week'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'starts_on' => Carbon::parse('2026-01-06'), // first Monday of 2026
                'ends_on' => null,
                'is_active' => true,
            ]);
        }

        return $recurrences;
    }

    /**
     * Create sessions spanning past 4 weeks + current week + next 2 weeks.
     */
    private function createSessions(User $psychologist, array $patients, array $recurrences): void
    {
        $now = Carbon::now();
        $sessionPrice = $psychologist->session_price;

        // ─── Past sessions (last 4 weeks) ─────────────────────────────
        for ($weekOffset = -4; $weekOffset <= -1; $weekOffset++) {
            foreach ($recurrences as $i => $recurrence) {
                $patient = $patients[$i];
                $sessionStart = $now->copy()
                    ->startOfWeek()
                    ->addWeeks($weekOffset)
                    ->next($this->dayOfWeekToCarbon($recurrence->day_of_week))
                    ->setTimeFromTimeString($recurrence->start_time);

                $sessionEnd = $sessionStart->copy()->addMinutes($psychologist->session_duration);

                // Vary statuses for realism
                $status = $this->randomPastStatus($weekOffset, $i);
                $price = $patient->session_price_override ?? $sessionPrice;

                $session = Session::create([
                    'psychologist_id' => $psychologist->id,
                    'patient_id' => $patient->id,
                    'recurrence_id' => $recurrence->id,
                    'starts_at' => $sessionStart,
                    'ends_at' => $sessionEnd,
                    'status' => $status,
                    'type' => $i === 2 ? 'online' : 'in_person', // Carla does online
                    'online_link' => $i === 2 ? 'https://meet.google.com/abc-def-ghi' : null,
                    'private_notes' => $status === 'completed' ? $this->sessionNote($patient->name, $weekOffset) : null,
                    'price' => $price,
                    'receipt_sent' => $status === 'completed',
                    'payment_status' => $status === 'completed' ? 'confirmed' : ($status === 'no_show' ? 'pending' : null),
                    'cancelled_at' => $status === 'cancelled' ? $sessionStart->copy()->subHours(fake()->numberBetween(2, 48)) : null,
                    'cancellation_reason' => $status === 'cancelled' ? 'Paciente solicitou remarcação.' : null,
                    'reschedule_count' => $status === 'cancelled' ? 1 : 0,
                    'confirmation_responded_at' => in_array($status, ['completed', 'confirmed']) ? $sessionStart->copy()->subHours(20) : null,
                ]);

                // Create payment for completed sessions
                if ($status === 'completed') {
                    Payment::create([
                        'psychologist_id' => $psychologist->id,
                        'patient_id' => $patient->id,
                        'session_id' => $session->id,
                        'amount' => $price,
                        'net_amount' => $price,
                        'fee' => 0,
                        'method' => 'pix',
                        'status' => 'confirmed',
                        'billing_mode' => 'post',
                        'due_date' => $sessionStart->toDateString(),
                        'paid_at' => $sessionStart->copy()->addHours(1),
                        'whatsapp_sent' => true,
                        'whatsapp_reminder_count' => 0,
                    ]);
                }

                // Create risk score event for no-shows
                if ($status === 'no_show') {
                    $currentScore = $patient->risk_score + 15;
                    RiskScoreEvent::create([
                        'patient_id' => $patient->id,
                        'session_id' => $session->id,
                        'event_type' => 'no_show',
                        'score_change' => 15,
                        'score_after' => $currentScore,
                    ]);
                }
            }
        }

        // ─── Carlos (non-recurring) — 2 past sessions ─────────────────
        for ($i = 0; $i < 2; $i++) {
            $sessionStart = $now->copy()->subWeeks(3 - $i)->next(Carbon::TUESDAY)->setTime(16, 0);
            $sessionEnd = $sessionStart->copy()->addMinutes(50);

            $session = Session::create([
                'psychologist_id' => $psychologist->id,
                'patient_id' => $patients[5]->id,
                'starts_at' => $sessionStart,
                'ends_at' => $sessionEnd,
                'status' => 'completed',
                'type' => 'in_person',
                'private_notes' => 'Paciente apresenta progresso na elaboração do luto. Diminuição dos episódios de choro.',
                'price' => $sessionPrice,
                'receipt_sent' => true,
                'payment_status' => 'confirmed',
            ]);

            Payment::create([
                'psychologist_id' => $psychologist->id,
                'patient_id' => $patients[5]->id,
                'session_id' => $session->id,
                'amount' => $sessionPrice,
                'net_amount' => $sessionPrice,
                'fee' => 0,
                'method' => 'pix',
                'status' => 'confirmed',
                'billing_mode' => 'post',
                'due_date' => $sessionStart->toDateString(),
                'paid_at' => $sessionStart->copy()->addHours(2),
                'whatsapp_sent' => true,
            ]);
        }

        // ─── Current week sessions ─────────────────────────────────────
        foreach ($recurrences as $i => $recurrence) {
            $patient = $patients[$i];
            $dayTarget = $this->dayOfWeekToCarbon($recurrence->day_of_week);

            $sessionStart = $now->copy()->startOfWeek()->next($dayTarget)->setTimeFromTimeString($recurrence->start_time);

            // If the session day already passed this week, mark as completed
            // If it's today or future, mark as scheduled/confirmed
            if ($sessionStart->isPast()) {
                $status = 'completed';
            } elseif ($sessionStart->isToday()) {
                $status = 'confirmed';
            } else {
                $status = 'scheduled';
            }

            $sessionEnd = $sessionStart->copy()->addMinutes($psychologist->session_duration);
            $price = $patient->session_price_override ?? $sessionPrice;

            $session = Session::create([
                'psychologist_id' => $psychologist->id,
                'patient_id' => $patient->id,
                'recurrence_id' => $recurrence->id,
                'starts_at' => $sessionStart,
                'ends_at' => $sessionEnd,
                'status' => $status,
                'type' => $i === 2 ? 'online' : 'in_person',
                'online_link' => $i === 2 ? 'https://meet.google.com/abc-def-ghi' : null,
                'price' => $price,
                'payment_status' => $status === 'completed' ? 'confirmed' : 'pending',
            ]);

            // Create reminders for upcoming sessions
            if (in_array($status, ['scheduled', 'confirmed'])) {
                Reminder::create([
                    'session_id' => $session->id,
                    'type' => '24h',
                    'confirmation_type' => 'interactive',
                    'status' => $sessionStart->diffInHours($now) <= 24 ? 'sent' : 'pending',
                    'scheduled_at' => $sessionStart->copy()->subHours(24),
                ]);

                Reminder::create([
                    'session_id' => $session->id,
                    'type' => '2h',
                    'confirmation_type' => 'info',
                    'status' => 'pending',
                    'scheduled_at' => $sessionStart->copy()->subHours(2),
                ]);
            }
        }

        // ─── Next 2 weeks (future sessions) ────────────────────────────
        for ($weekOffset = 1; $weekOffset <= 2; $weekOffset++) {
            foreach ($recurrences as $i => $recurrence) {
                $patient = $patients[$i];
                $sessionStart = $now->copy()
                    ->startOfWeek()
                    ->addWeeks($weekOffset)
                    ->next($this->dayOfWeekToCarbon($recurrence->day_of_week))
                    ->setTimeFromTimeString($recurrence->start_time);

                $sessionEnd = $sessionStart->copy()->addMinutes($psychologist->session_duration);
                $price = $patient->session_price_override ?? $sessionPrice;

                Session::create([
                    'psychologist_id' => $psychologist->id,
                    'patient_id' => $patient->id,
                    'recurrence_id' => $recurrence->id,
                    'starts_at' => $sessionStart,
                    'ends_at' => $sessionEnd,
                    'status' => 'scheduled',
                    'type' => $i === 2 ? 'online' : 'in_person',
                    'online_link' => $i === 2 ? 'https://meet.google.com/abc-def-ghi' : null,
                    'price' => $price,
                    'payment_status' => 'pending',
                ]);
            }
        }

        // ─── Lucas (adolescent) — biweekly Wednesdays ──────────────────
        for ($weekOffset = 0; $weekOffset <= 3; $weekOffset += 2) {
            $sessionStart = $now->copy()
                ->startOfWeek()
                ->addWeeks($weekOffset)
                ->next(Carbon::WEDNESDAY)
                ->setTime(16, 0);

            $sessionEnd = $sessionStart->copy()->addMinutes(50);

            Session::create([
                'psychologist_id' => $psychologist->id,
                'patient_id' => $patients[7]->id,
                'starts_at' => $sessionStart,
                'ends_at' => $sessionEnd,
                'status' => $sessionStart->isPast() ? 'completed' : 'scheduled',
                'type' => 'in_person',
                'price' => $sessionPrice,
                'payment_status' => $sessionStart->isPast() ? 'confirmed' : 'pending',
            ]);
        }
    }

    /**
     * Create waiting list entries.
     */
    private function createWaitingListEntries(User $psychologist, array $patients): void
    {
        // Carlos is on the waiting list for a regular slot
        WaitingListEntry::create([
            'psychologist_id' => $psychologist->id,
            'patient_id' => $patients[5]->id,
            'preferred_days' => [2, 3], // Tuesday, Wednesday
            'preferred_period' => 'afternoon',
            'status' => 'waiting',
            'position' => 1,
        ]);
    }

    /**
     * Create LGPD consents for all active patients.
     */
    private function createConsents(array $patients): void
    {
        foreach ($patients as $patient) {
            if (! $patient->is_active) {
                continue;
            }

            Consent::create([
                'patient_id' => $patient->id,
                'type' => 'data_processing',
                'granted' => true,
                'granted_at' => $patient->created_at,
                'ip_address' => '189.100.'.fake()->numberBetween(1, 255).'.'.fake()->numberBetween(1, 255),
            ]);

            Consent::create([
                'patient_id' => $patient->id,
                'type' => 'whatsapp_communication',
                'granted' => true,
                'granted_at' => $patient->created_at,
                'ip_address' => '189.100.'.fake()->numberBetween(1, 255).'.'.fake()->numberBetween(1, 255),
            ]);

            // Only some patients consented to AI screening
            if (in_array($patient->ai_screening_status, ['completed', 'in_progress'])) {
                Consent::create([
                    'patient_id' => $patient->id,
                    'type' => 'ai_screening',
                    'granted' => true,
                    'granted_at' => $patient->created_at,
                    'ip_address' => '189.100.'.fake()->numberBetween(1, 255).'.'.fake()->numberBetween(1, 255),
                ]);
            }
        }
    }

    // ─── Helper Methods ─────────────────────────────────────────────────

    /**
     * Convert day_of_week integer to Carbon constant.
     */
    private function dayOfWeekToCarbon(int $day): int
    {
        return match ($day) {
            0 => Carbon::SUNDAY,
            1 => Carbon::MONDAY,
            2 => Carbon::TUESDAY,
            3 => Carbon::WEDNESDAY,
            4 => Carbon::THURSDAY,
            5 => Carbon::FRIDAY,
            6 => Carbon::SATURDAY,
        };
    }

    /**
     * Return a realistic past session status based on week offset and patient index.
     */
    private function randomPastStatus(int $weekOffset, int $patientIndex): string
    {
        // Most are completed, but sprinkle in some variety
        $statusPool = [
            'completed', 'completed', 'completed', 'completed', // 80% completed
            'cancelled',
            'no_show',
        ];

        // João (index 1) had a no_show 3 weeks ago
        if ($patientIndex === 1 && $weekOffset === -3) {
            return 'no_show';
        }

        // Carla (index 2) cancelled 2 weeks ago
        if ($patientIndex === 2 && $weekOffset === -2) {
            return 'cancelled';
        }

        return 'completed';
    }

    /**
     * Generate a realistic session note.
     */
    private function sessionNote(string $patientName, int $weekOffset): string
    {
        $notes = [
            'Paciente relata melhora no padrão de sono. Mantida a reestruturação cognitiva. Tarefa: registro de pensamentos automáticos.',
            'Sessão focada em exposição gradual. Paciente demonstrou ansiedade 7/10 inicial, reduzida a 3/10 ao final. Progresso satisfatório.',
            'Trabalhamos técnicas de respiração diafragmática. Paciente conseguiu aplicar no dia anterior durante crise de ansiedade.',
            'Paciente chegou emocionado. Processamento de evento significativo da semana. Acolhimento e validação emocional.',
            'Revisão dos registros de pensamento. Identificados 3 padrões de catastrofização. Trabalhamos evidências a favor e contra.',
            'Sessão produtiva. Paciente relata utilização eficaz das técnicas de relaxamento. Agenda da próxima sessão: assertividade.',
            'Psicoeducação sobre distorções cognitivas (rotulação e filtro mental). Paciente demonstrou boa compreensão dos conceitos.',
            'Aplicação de técnica de resolução de problemas para conflito no trabalho. Paciente identificou 4 alternativas de ação.',
        ];

        $index = abs($weekOffset) % count($notes);

        return $notes[$index];
    }
}
