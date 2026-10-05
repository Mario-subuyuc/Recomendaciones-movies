@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'errorBag' => 'default'])
<div class="mb-3">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" @if ($type !== 'password') value="{{ old($name, $value) }}" @endif @required($required) {{ $attributes->class(['form-control', 'is-invalid' => $errors->getBag($errorBag)->has($name)]) }}>
    @error($name, $errorBag)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
