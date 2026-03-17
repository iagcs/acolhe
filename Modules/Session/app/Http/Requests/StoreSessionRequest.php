<?php

namespace Modules\Session\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Session\DTOs\SessionData;
use Modules\Session\Rules\NoOverlappingSession;
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
            'patient_id' => [
                'required',
                'uuid',
                Rule::exists('patients', 'id')->where('psychologist_id', $this->user()->id),
            ],
            'scheduled_at' => [
                'required',
                'date',
                'after:now',
                new NoOverlappingSession(userId: $this->user()->id),
            ],
            'duration_minutes' => ['sometimes', 'integer', 'min:15', 'max:180'],
            'type' => ['required', 'in:online,in_person'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
