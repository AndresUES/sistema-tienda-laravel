<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Historial de Compras') }}
            </h2>
            <a href="{{ route('compras.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Registrar Nueva Compra
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Fecha</th>
                                    <th scope="col" class="px-6 py-3">Proveedor</th>
                                    <th scope="col" class="px-6 py-3">N° Factura</th>
                                    <th scope="col" class="px-6 py-3 text-right">Total</th>
                                    <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($compras as $compra)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $compra->id }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $compra->proveedor->nombre }}</td>
                                    <td class="px-6 py-4">{{ $compra->numero_factura }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        ${{ number_format($compra->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('compras.show', $compra->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 focus:outline-none">
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No se han registrado compras aún.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $compras->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>