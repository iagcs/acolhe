<?php

namespace Modules\Session\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Session\DTOs\SessionData;
use Spatie\LaravelData\WithData;

class StoreSessionRequest extends FormRequest
{
    use WithData;

    protected string $dataClass = SessionData::class;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'uuid', 'exists:patients,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['sometimes', 'integer', 'min:15', 'max:180'],
            'type' => ['required', 'in:online,in_person'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
