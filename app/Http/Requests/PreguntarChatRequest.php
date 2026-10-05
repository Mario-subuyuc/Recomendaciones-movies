<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreguntarChatRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('pregunta'))) {
            $this->merge(['pregunta' => trim($this->input('pregunta'))]);
        }
    }

    public function authorize(): bool
    {
        $categoria = $this->input('categoria');

        return ! in_array($categoria, ['peliculas', 'videojuegos'], true) || $this->user()->can($categoria.'.ver');
    }

    public function rules(): array
    {
        return ['categoria' => ['required', Rule::in(['peliculas', 'videojuegos'])], 'pregunta' => ['required', 'string', 'max:1000', 'regex:/\S/u']];
    }
}
