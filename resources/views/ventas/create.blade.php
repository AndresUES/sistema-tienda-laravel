<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Venta</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('ventas.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded border">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                                <select name="cliente_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                    <option value="">-- Seleccione Cliente --</option>
                                    @foreach($clientes as $cli)
                                        <option value="{{ $cli->id }}">
                                            {{ $cli->nombre }} 
                                            ({{ $cli->nit ?? ($cli->dui ?? 'S/D') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo Comprobante</label>
                                <select name="tipo_documento" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="FCF">Consumidor Final</option>
                                    <option value="CCF">Crédito Fiscal</option>
                                    <option value="TICKET">Ticket</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">N° Correlativo (Auto)</label>
                                <input type="text" 
                                       name="numero_factura" 
                                       value="{{ $numeroFactura }}" 
                                       class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-gray-600 font-bold cursor-not-allowed" 
                                       readonly>
                            </div>
                        </div>

                        <div class="mb-6 p-4 border border-green-100 bg-green-50 rounded-lg">
                            <h3 class="text-sm font-bold text-green-800 mb-2">Agregar Productos</h3>
                            
                            <div class="mb-2">
                                <label class="text-xs text-gray-600 font-bold">🔍 Buscar Producto:</label>
                                <input type="text" id="buscador" class="w-full border-green-300 ring-2 ring-green-100 rounded-md shadow-sm placeholder-gray-400" placeholder="Escriba nombre o código..." autocomplete="off">
                            </div>

                            <div class="flex flex-col md:flex-row gap-2 items-end">
                                <div class="w-full md:w-5/12">
                                    <label class="text-xs text-gray-600">Seleccionar</label>
                                    <select id="select_producto" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($productos as $prod)
                                            <option value="{{ $prod->id }}" 
                                                    data-precio="{{ $prod->precio_venta }}" 
                                                    data-stock="{{ $prod->stock }}">
                                                {{ $prod->codigo_barra }} - {{ $prod->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Stock</label>
                                    <input type="text" id="stock_display" class="w-full bg-gray-200 border-gray-300 rounded-md shadow-sm text-center font-bold" readonly value="0">
                                </div>

                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Precio ($)</label>
                                    <input type="text" id="precio_display" class="w-full bg-gray-200 border-gray-300 rounded-md shadow-sm text-center" readonly value="0.00">
                                </div>

                                <div class="w-full md:w-1/12">
                                    <label class="text-xs text-gray-600">Cant.</label>
                                    <input type="number" id="input_cantidad" class="w-full border-gray-300 rounded-md shadow-sm text-center" value="1" min="1">
                                </div>

                                <div class="w-full md:w-2/12">
                                    <button type="button" onclick="agregarFila()" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 font-bold shadow">
                                        + Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto mb-6 border rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Producto</th>
                                        <th class="px-6 py-3 text-right">Precio</th>
                                        <th class="px-6 py-3 text-center">Cant.</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                        <th class="px-6 py-3 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_detalle"></tbody>
                                <tfoot>
                                    <tr class="font-bold text-gray-900 bg-gray-50 text-lg">
                                        <td colspan="3" class="px-6 py-4 text-right">TOTAL:</td>
                                        <td class="px-6 py-4 text-right text-green-700" id="total_pagar">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btn_guardar" class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-bold shadow-lg" disabled>
                                Finalizar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let total = 0;
        const buscador = document.getElementById('buscador');
        const selector = document.getElementById('select_producto');
        const opcionesOriginales = Array.from(selector.options);

        function actualizarDatosProducto() {
            if (selector.selectedIndex >= 0) {
                const opcion = selector.options[selector.selectedIndex];
                const precio = opcion.getAttribute('data-precio');
                const stock = opcion.getAttribute('data-stock');

                if (precio && stock) {
                    document.getElementById('precio_display').value = parseFloat(precio).toFixed(2);
                    document.getElementById('stock_display').value = stock;
                    document.getElementById('input_cantidad').max = stock;
                } else {
                    document.getElementById('precio_display').value = "0.00";
                    document.getElementById('stock_display').value = "0";
                }
            }
        }

        buscador.addEventListener('keyup', function() {
            const texto = this.value.toLowerCase();
            selector.innerHTML = '';
            const filtradas = opcionesOriginales.filter(op => op.text.toLowerCase().includes(texto));
            
            filtradas.forEach(op => selector.add(op));

            if (filtradas.length === 0 || texto === '') {
                selector.innerHTML = '';
                opcionesOriginales.forEach(op => selector.add(op));
            }
            if (selector.options.length > 0) {
                selector.selectedIndex = 0;
                actualizarDatosProducto();
            }
        });

        selector.addEventListener('change', actualizarDatosProducto);

        function agregarFila() {
            const id = selector.value;
            const texto = selector.options[selector.selectedIndex].text;
            const precio = parseFloat(document.getElementById('precio_display').value);
            const stock = parseInt(document.getElementById('stock_display').value);
            const cantidad = parseInt(document.getElementById('input_cantidad').value);

            if (!id) { alert("Seleccione un producto"); return; }
            if (cantidad <= 0) { alert("Cantidad inválida"); return; }
            if (cantidad > stock) { 
                alert("¡Stock insuficiente! Solo quedan " + stock + " unidades."); 
                return; 
            }

            const subtotal = precio * cantidad;
            total += subtotal;

            const fila = `
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <input type="hidden" name="productos_id[]" value="${id}">
                        ${texto}
                    </td>
                    <td class="px-6 py-4 text-right">$${precio.toFixed(2)}</td>
                    <td class="px-6 py-4 text-center">
                        <input type="hidden" name="cantidades[]" value="${cantidad}">
                        ${cantidad}
                    </td>
                    <td class="px-6 py-4 text-right font-bold">$${subtotal.toFixed(2)}</td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" onclick="eliminar(this, ${subtotal})" class="text-red-600 font-bold">X</button>
                    </td>
                </tr>
            `;

            document.getElementById('tabla_detalle').insertAdjacentHTML('beforeend', fila);
            actualizarTotal();
            
            // Resetear
            buscador.value = "";
            document.getElementById('input_cantidad').value = 1;
            selector.innerHTML = '';
            opcionesOriginales.forEach(op => selector.add(op));
            actualizarDatosProducto();
        }

        function eliminar(btn, subtotal) {
            btn.closest('tr').remove();
            total -= subtotal;
            actualizarTotal();
        }

        function actualizarTotal() {
            document.getElementById('total_pagar').innerText = '$' + total.toFixed(2);
            document.getElementById('btn_guardar').disabled = total <= 0;
        }
    </script>
</x-app-layout>