<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarCatalogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $module = $this->routeIs('peliculas.*') ? 'peliculas' : 'videojuegos';

        return $this->user()->can($module.'.'.($this->isMethod('POST') ? 'crear' : 'editar'));
    }

    public function rules(): array
    {
        $rules = [
            'titulo' => ['required', 'string', 'max:255'],
            'genero' => ['required', 'string', 'max:255'],
            'plataforma' => ['required', 'string', 'max:255'],
            'anio_lanzamiento' => ['required', 'integer', 'between:1888,2100'],
            'calificacion' => ['required', 'numeric', 'between:0,10', 'decimal:0,1'],
            'fecha_registro' => ['required', 'date_format:Y-m-d'],
        ];

        return array_merge($rules, $this->routeIs('peliculas.*') ? [
            'director' => ['required', 'string', 'max:255'],
            'actores' => ['required', 'string', 'max:5000'],
            'productora' => ['required', 'string', 'max:255'],
            'duracion_minutos' => ['required', 'integer', 'between:1,10000'],
            'clasificacion' => ['required', 'string', 'max:255'],
        ] : [
            'desarrollador' => ['required', 'string', 'max:255'],
            'jugadores' => ['required', 'integer', 'between:1,100000'],
        ]);
    }
}
