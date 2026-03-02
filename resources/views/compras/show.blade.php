<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle de Compra') }} #{{ $compra->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    <div class="flex justify-between border-b pb-6 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-700 uppercase">{{ $compra->proveedor->nombre }}</h3>
                            <p class="text-sm text-gray-500">NIT: {{ $compra->proveedor->nit }}</p>
                            <p class="text-sm text-gray-500 mt-2">
                                <span class="font-bold">Factura Nº:</span> {{ $compra->numero_factura }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">
                                <span class="font-bold">Fecha:</span> {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Registrado por: {{ $compra->usuario->name ?? 'Usuario' }}
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3">Producto</th>
                                    <th class="px-6 py-3 text-right">Costo Unit.</th>
                                    <th class="px-6 py-3 text-center">Cant.</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($compra->detalles as $detalle)
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $detalle->producto->codigo_barra }} - {{ $detalle->producto->nombre }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        ${{ number_format($detalle->precio, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ $detalle->cantidad }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-700">
                                        ${{ number_format($detalle->subtotal, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-2 text-right font-bold">Subtotal:</td>
                                    <td class="px-6 py-2 text-right">${{ number_format($compra->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-2 text-right font-bold">IVA (13%):</td>
                                    <td class="px-6 py-2 text-right">${{ number_format($compra->iva, 2) }}</td>
                                </tr>
                                <tr class="border-t-2 border-gray-300">
                                    <td colspan="3" class="px-6 py-3 text-right font-black text-gray-900 text-lg">TOTAL:</td>
                                    <td class="px-6 py-3 text-right font-black text-blue-700 text-lg">
                                        ${{ number_format($compra->total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('compras.index') }}" class="text-blue-600 hover:underline font-medium">
                            &larr; Volver al historial de compras
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>