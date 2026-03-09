<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle de Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border border-gray-200">
                <div class="p-8 text-gray-900 relative">
                    
                    @if($venta->estado == 'ANULADA')
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-20 rotate-45 pointer-events-none">
                        <span class="text-9xl font-black text-red-600 border-8 border-red-600 px-10 rounded">ANULADA</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-start border-b pb-6 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold uppercase tracking-widest text-gray-800">TIENDA DEMO</h1>
                            <p class="text-sm text-gray-500">Comprobante de Venta Electrónico</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-xl font-bold text-gray-700">{{ $venta->tipo_documento }}</h3>
                            <p class="text-lg text-red-600 font-mono">Nº {{ $venta->numero_factura }}</p>
                            <p class="text-sm text-gray-500 mt-1">Fecha: {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
                        <div>
                            <h4 class="font-bold text-gray-700 uppercase mb-2 border-b w-1/2">Cliente</h4>
                            <p class="text-lg font-bold">{{ $venta->cliente->nombre }}</p>
                            
                            @if($venta->cliente->nit)
                                <p><span class="font-semibold">NIT:</span> {{ $venta->cliente->nit }}</p>
                            @endif
                            
                            @if($venta->cliente->nrc)
                                <p><span class="font-semibold">NRC:</span> {{ $venta->cliente->nrc }}</p>
                            @endif
                            
                            @if(!$venta->cliente->nit && !$venta->cliente->nrc)
                                <p><span class="font-semibold">DUI:</span> {{ $venta->cliente->dui ?? 'N/A' }}</p>
                            @endif
                            
                            <p class="mt-1 text-gray-500">{{ $venta->cliente->direccion }}</p>
                        </div>
                        <div class="text-right">
                            <h4 class="font-bold text-gray-700 uppercase mb-2 border-b inline-block">Atendido Por</h4>
                            <p class="text-lg">{{ $venta->usuario->name }}</p>
                            <p class="text-gray-500">{{ $venta->usuario->email }}</p>
                            
                            <div class="mt-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $venta->estado == 'ACTIVA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    ESTADO: {{ $venta->estado }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-100 text-gray-800 uppercase font-bold text-xs">
                                <tr>
                                    <th class="px-4 py-3 text-center">Cant.</th>
                                    <th class="px-4 py-3">Descripción</th>
                                    <th class="px-4 py-3 text-right">Precio Unit.</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($venta->detalles as $detalle)
                                <tr>
                                    <td class="px-4 py-3 text-center font-bold">{{ $detalle->cantidad }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $detalle->producto->nombre }}</div>
                                        <div class="text-xs text-gray-500">Cód: {{ $detalle->producto->codigo_barra }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">${{ number_format($detalle->precio, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end border-t pt-4">
                        <div class="w-64">
                            <div class="flex justify-between py-1 text-sm text-gray-600">
                                <span>Subtotal:</span>
                                <span>${{ number_format($venta->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-1 text-sm text-gray-600">
                                <span>IVA (13%):</span>
                                <span>${{ number_format($venta->iva, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-2 mt-2 border-t border-gray-300 text-xl font-black text-gray-900">
                                <span>TOTAL:</span>
                                <span>${{ number_format($venta->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center print:hidden space-x-4">
                        <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-md hover:bg-gray-700 font-bold shadow-md transition duration-150 ease-in-out">
                            🖨️ Imprimir Comprobante
                        </button>
                        
                        <a href="{{ route('ventas.index') }}" class="text-gray-600 hover:text-gray-900 underline font-medium">
                            Volver al Listado
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-3xl, .max-w-3xl * {
                visibility: visible;
            }
            .max-w-3xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .print\:hidden {
                display: none !important;
            }
            /* Ocultar header y nav de Laravel Breeze si existen */
            nav, header { 
                display: none !important; 
            }
        }
    </style>
</x-app-layout>