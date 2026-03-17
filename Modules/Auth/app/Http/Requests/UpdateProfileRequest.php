<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\DTOs\UpdateProfileData;
use Spatie\LaravelData\WithData;

class UpdateProfileRequest extends FormRequest
{
    use WithData;

    protected string $dataClass = UpdateProfileData::class;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
