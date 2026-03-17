<?php

namespace Modules\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'patient_id' => [
                'required',
                'uuid',
                Rule::exists('patients', 'id')->where('psychologist_id', $user->id),
            ],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:20480'],
            'name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'O paciente é obrigatório.',
            'patient_id.exists' => 'Paciente não encontrado.',
            'file.required' => 'O arquivo é obrigatório.',
            'file.mimes' => 'O arquivo deve ser PDF, imagem (JPG/PNG) ou documento Word.',
            'file.max' => 'O arquivo não pode ser maior que 20 MB.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'category.max' => 'A categoria não pode ter mais de 100 caracteres.',
        ];
    }
}
