<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Venta #{{ $venta->numero_factura }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8"> <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border border-gray-200">
                <div class="p-8 text-gray-900">
                    
                    <div class="text-center border-b pb-4 mb-4">
                        <h1 class="text-2xl font-bold uppercase tracking-widest">TIENDA DEMO</h1>
                        <p class="text-sm text-gray-500">Comprobante: {{ $venta->tipo_documento }} - {{ $venta->numero_factura }}</p>
                        <p class="text-sm text-gray-500">Fecha: {{ $venta->fecha }}</p>
                    </div>

                    <div class="mb-6 text-sm">
                        <p><span class="font-bold">Cliente:</span> {{ $venta->cliente->nombre }}</p>
                        <p><span class="font-bold">Doc:</span> {{ $venta->cliente->nit ?? $venta->cliente->dui }}</p>
                        <p><span class="font-bold">Atendido por:</span> {{ $venta->usuario->name }}</p>
                    </div>

                    <table class="w-full text-sm mb-6">
                        <thead class="border-b-2 border-gray-300">
                            <tr>
                                <th class="text-left py-2">Cant</th>
                                <th class="text-left py-2">Descripción</th>
                                <th class="text-right py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->detalles as $detalle)
                            <tr class="border-b border-dashed border-gray-200">
                                <td class="py-2">{{ $detalle->cantidad }}</td>
                                <td class="py-2">{{ $detalle->producto->nombre }}</td>
                                <td class="py-2 text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-right">
                        <p class="text-sm">Subtotal: ${{ number_format($venta->subtotal, 2) }}</p>
                        <p class="text-sm">IVA (13%): ${{ number_format($venta->iva, 2) }}</p>
                        <p class="text-xl font-black mt-2">TOTAL: ${{ number_format($venta->total, 2) }}</p>
                    </div>

                    <div class="mt-8 text-center no-print">
                        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Imprimir Comprobante
                        </button>
                        <a href="{{ route('ventas.index') }}" class="ml-4 text-gray-600 underline">Volver</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>