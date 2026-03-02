<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Proveedor') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('proveedores.update', $proveedor->id) }}">
                        @csrf @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="nombre" :value="__('Nombre de la Empresa')" />
                                <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $proveedor->nombre)" required />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nit" :value="__('NIT')" />
                                <x-text-input id="nit" class="block mt-1 w-full" type="text" name="nit" :value="old('nit', $proveedor->nit)" required />
                                <x-input-error :messages="$errors->get('nit')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nrc" :value="__('NRC')" />
                                <x-text-input id="nrc" class="block mt-1 w-full" type="text" name="nrc" :value="old('nrc', $proveedor->nrc)" required />
                                <x-input-error :messages="$errors->get('nrc')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefono" :value="__('Teléfono')" />
                                <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $proveedor->telefono)" required />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Correo Electrónico')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $proveedor->email)" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="direccion" :value="__('Dirección')" />
                            <textarea id="direccion" name="direccion" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2">{{ old('direccion', $proveedor->direccion) }}</textarea>
                        </div>

                        <div class="flex justify-end mt-4">
                            <a href="{{ route('proveedores.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>Actualizar Proveedor</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>