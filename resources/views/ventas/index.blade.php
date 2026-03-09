<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ventas Realizadas</h2>
            <a href="{{ route('ventas.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase font-bold">Nueva Venta</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">N° Doc</th>
                                <th class="px-6 py-3">Cliente</th>
                                <th class="px-6 py-3">Vendedor</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ventas as $venta)
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4">{{ $venta->fecha }}</td>
                                <td class="px-6 py-4">{{ $venta->tipo_documento }} - {{ $venta->numero_factura }}</td>
                                <td class="px-6 py-4">{{ $venta->cliente->nombre }}</td>
                                <td class="px-6 py-4">{{ $venta->usuario->name }}</td>
                                <td class="px-6 py-4 font-bold text-green-600">${{ number_format($venta->total, 2) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('ventas.show', $venta->id) }}" class="text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $ventas->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>