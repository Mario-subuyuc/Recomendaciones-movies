<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarCatalogoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

abstract class CatalogoController extends Controller
{
    protected string $model;

    protected string $module;

    protected string $title;

    protected array $fields;

    protected function data(array $extra = []): array
    {
        return array_merge(['module' => $this->module, 'title' => $this->title, 'fields' => $this->fields], $extra);
    }

    public function index(): View
    {
        return view('catalogo.listado', $this->data(['records' => $this->model::orderByDesc((new $this->model)->getKeyName())->paginate(15)]));
    }

    public function create(): View
    {
        return view('catalogo.formulario', $this->data(['record' => new $this->model]));
    }

    public function store(GuardarCatalogoRequest $request): RedirectResponse
    {
        $this->model::create($request->validated());

        return to_route($request->user()->can($this->module.'.ver') ? $this->module.'.index' : 'dashboard')->with('status', 'Registro creado correctamente.');
    }

    public function show(string $record): View
    {
        return view('catalogo.detalle', $this->data(['record' => $this->model::findOrFail($record)]));
    }

    public function edit(string $record): View
    {
        return view('catalogo.formulario', $this->data(['record' => $this->model::findOrFail($record)]));
    }

    public function update(GuardarCatalogoRequest $request, string $record): RedirectResponse
    {
        $this->model::findOrFail($record)->update($request->validated());

        return to_route($request->user()->can($this->module.'.ver') ? $this->module.'.index' : 'dashboard')->with('status', 'Registro actualizado correctamente.');
    }

    public function destroy(string $record): RedirectResponse
    {
        $this->model::findOrFail($record)->delete();

        return to_route(auth()->user()->can($this->module.'.ver') ? $this->module.'.index' : 'dashboard')->with('status', 'Registro eliminado correctamente.');
    }
}
