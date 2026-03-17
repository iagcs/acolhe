<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\DTOs\LoginData;
use Spatie\LaravelData\WithData;

class LoginRequest extends FormRequest
{
    use WithData;

    protected string $dataClass = LoginData::class;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }
}
