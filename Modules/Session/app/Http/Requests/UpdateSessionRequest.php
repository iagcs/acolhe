<?php

namespace Modules\Session\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Session\DTOs\UpdateSessionData;
use Modules\Session\Rules\NoOverlappingSession;
use Spatie\LaravelData\WithData;

class UpdateSessionRequest extends FormRequest
{
    use WithData;

    protected string $dataClass = UpdateSessionData::class;

    /**
     * Allow all incoming requests for this form request.
     *
     * @return bool `true` if the request is authorized, `false` otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Build the validation rules used to validate an incoming session update request.
     *
     * The rules cover duration_minutes, type, notes, status, cancellation_reason (required when status is `cancelled`), and scheduled_at (must be a date after now). If an existing session owned by the current user is being updated, the scheduled_at rules include a NoOverlappingSession rule that excludes the current session's time range.
     *
     * @return array<string, mixed> Validation rules keyed by field name.
     */
    public function rules(): array
    {
        $session = $this->route('session');
        $sessionModel = $session
            ? \Modules\Session\Models\Session::where('psychologist_id', $this->user()->id)->find($session)
            : null;

        $rules = [
            'duration_minutes' => ['sometimes', 'integer', 'min:15', 'max:180'],
            'type' => ['sometimes', 'in:online,in_person'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'in:scheduled,pending_confirmation,confirmed,cancelled,completed,no_show'],
            'cancellation_reason' => ['nullable', 'string', 'required_if:status,cancelled'],
        ];

        $rules['scheduled_at'] = ['sometimes', 'date', 'after:now'];

        if ($sessionModel) {
            $rules['scheduled_at'][] = new NoOverlappingSession(
                userId: $this->user()->id,
                excludeSessionId: $sessionModel->id,
                existingStartsAt: $sessionModel->starts_at,
                existingEndsAt: $sessionModel->ends_at,
            );
        }

        return $rules;
    }
}
