<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class GuardarUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('usuarios.'.($this->isMethod('POST') ? 'crear' : 'editar'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', Rule::unique(User::class)->ignore($this->route('user'))],
            'password' => [$this->isMethod('POST') ? 'required' : 'nullable', 'confirmed', Password::defaults()],
            'roles' => $this->user()->hasRole('Administrador') ? ['required', 'array', 'size:1'] : ['prohibited'],
            'roles.*' => ['required', 'string', 'distinct', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['prohibited'],
        ];
    }
}
