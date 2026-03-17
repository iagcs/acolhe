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

    public function authorize(): bool
    {
        return true;
    }

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
