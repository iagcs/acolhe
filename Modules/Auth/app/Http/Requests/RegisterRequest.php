<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Auth\DTOs\RegisterData;
use Modules\Auth\Enums\TherapeuticApproach;
use Spatie\LaravelData\WithData;

class RegisterRequest extends FormRequest
{
    use WithData;

    protected string $dataClass = RegisterData::class;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:psychologists'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'crp' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'therapeutic_approach' => ['required', Rule::enum(TherapeuticApproach::class)],
            'session_duration' => ['required', 'integer', 'min:15', 'max:180'],
            'session_interval' => ['required', 'integer', 'min:0', 'max:60'],
            'session_price' => ['required', 'numeric', 'min:0'],
            'availabilities' => ['required', 'array', 'min:1'],
            'availabilities.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'availabilities.*.start_time' => ['required', 'date_format:H:i'],
            'availabilities.*.end_time' => ['required', 'date_format:H:i', 'after:availabilities.*.start_time'],
        ];
    }
}
