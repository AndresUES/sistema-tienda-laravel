<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Historial de Ventas</h2>
            <a href="{{ route('ventas.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase font-bold hover:bg-gray-700">
                Nueva Venta
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Fecha</th>
                                <th class="px-6 py-3">N° Doc</th>
                                <th class="px-6 py-3">Cliente</th>
                                <th class="px-6 py-3">Tipo</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ventas as $venta)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 font-mono">{{ $venta->numero_factura }}</td>
                                <td class="px-6 py-4 font-bold">{{ $venta->cliente->nombre }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-500">
                                        {{ $venta->tipo_documento }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-green-600">${{ number_format($venta->total, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('ventas.show', $venta->id) }}" class="text-blue-600 hover:underline">Ver Recibo</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4">No hay ventas registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $ventas->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>