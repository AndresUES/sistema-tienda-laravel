<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Inventario de Productos') }}</h2>
            <a href="{{ route('productos.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 text-white">
                Nuevo Producto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Código</th>
                                    <th class="px-6 py-3">Producto</th>
                                    <th class="px-6 py-3">Categoría</th>
                                    <th class="px-6 py-3">Marca</th>
                                    <th class="px-6 py-3">Stock</th>
                                    <th class="px-6 py-3">Precio Venta</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productos as $producto)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono">{{ $producto->codigo_barra }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $producto->nombre }}</td>
                                    <td class="px-6 py-4">{{ $producto->categoria?->nombre }}</td>
                                    <td class="px-6 py-4">{{ $producto->marca?->nombre }}</td>
                                    
                                    <td class="px-6 py-4">
                                        @if($producto->stock <= $producto->stock_minimo)
                                            <span class="text-red-600 font-bold">{{ $producto->stock }}</span>
                                        @else
                                            <span class="text-green-600 font-bold">{{ $producto->stock }}</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">${{ number_format($producto->precio_venta, 2) }}</td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('productos.edit', $producto->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2">Editar</a>
                                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Eliminar producto?');" class="inline-block m-0 p-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-2">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4">No hay productos registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $productos->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>