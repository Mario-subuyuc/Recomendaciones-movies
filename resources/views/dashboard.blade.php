<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                    @role('Administrador')
                        <div class="mt-6">
                            <h3 class="font-semibold text-lg">Usuarios</h3>
                            <p class="mt-2">Crea y administra usuarios, roles y permisos adicionales.</p>
                            <a class="inline-block mt-3 underline" href="{{ route('admin.usuarios.index') }}">Administrar usuarios</a>
                        </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
