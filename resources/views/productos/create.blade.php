<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Producto') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('productos.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div>
                                <x-input-label for="codigo_barra" :value="__('Código de Barra')" />
                                <x-text-input id="codigo_barra" class="block mt-1 w-full" type="text" name="codigo_barra" :value="old('codigo_barra')" required />
                                <x-input-error :messages="$errors->get('codigo_barra')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nombre" :value="__('Nombre del Producto')" />
                                <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="categoria_id" :value="__('Categoría')" />
                                <select id="categoria_id" name="categoria_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="marca_id" :value="__('Marca')" />
                                <select id="marca_id" name="marca_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Seleccione una marca</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('marca_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="precio_compra" :value="__('Costo ($)')" />
                                <x-text-input id="precio_compra" class="block mt-1 w-full" type="number" step="0.01" name="precio_compra" :value="old('precio_compra')" required />
                            </div>

                            <div>
                                <x-input-label for="precio_venta" :value="__('Precio Venta ($)')" />
                                <x-text-input id="precio_venta" class="block mt-1 w-full" type="number" step="0.01" name="precio_venta" :value="old('precio_venta')" required />
                            </div>

                            <div>
                                <x-input-label for="stock" :value="__('Stock Inicial')" />
                                <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock', 0)" required />
                            </div>

                            <div>
                                <x-input-label for="stock_minimo" :value="__('Alerta Stock Mínimo')" />
                                <x-text-input id="stock_minimo" class="block mt-1 w-full" type="number" name="stock_minimo" :value="old('stock_minimo', 5)" />
                            </div>

                        </div>

                        <div class="mt-4">
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" name="descripcion" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="flex justify-end mt-4">
                            <a href="{{ route('productos.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>Guardar Producto</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>