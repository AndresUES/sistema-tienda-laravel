<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Venta (Facturación)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded border">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                                <select name="cliente_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="">Seleccione Cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo Documento</label>
                                <select name="tipo_documento" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                    <option value="FCF">Factura Consumidor Final</option>
                                    <option value="CCF">Comprobante Crédito Fiscal</option>
                                    <option value="TICKET">Ticket</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">N° Documento</label>
                                <input type="text" name="numero_factura" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required placeholder="Ej: 001-001-000001">
                            </div>
                        </div>

                        <div class="mb-6 p-4 border border-green-100 bg-green-50 rounded-lg">
                            <h3 class="text-sm font-bold text-green-800 mb-2">Carrito de Venta</h3>
                            <div class="flex flex-col md:flex-row gap-2 items-end">
                                
                                <div class="w-full md:w-5/12">
                                    <label class="text-xs text-gray-600">Buscar Producto</label>
                                    <select id="producto_select" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                        <option value="">Seleccione...</option>
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
                                    <label class="text-xs text-gray-600">Stock Dispon.</label>
                                    <input type="text" id="stock_display" class="bg-gray-200 border-gray-300 rounded-md shadow-sm w-full text-sm text-center font-bold" readonly value="0">
                                </div>

                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Precio Unit.</label>
                                    <input type="text" id="precio_display" class="bg-gray-200 border-gray-300 rounded-md shadow-sm w-full text-sm text-center" readonly value="$0.00">
                                </div>

                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Cantidad</label>
                                    <input type="number" id="cantidad_input" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm" value="1" min="1">
                                </div>

                                <div class="w-full md:w-2/12">
                                    <button type="button" onclick="agregarAlCarrito()" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm font-bold shadow">
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
                                <tbody id="tabla_ventas">
                                    </tbody>
                                <tfoot class="border-t-2 border-gray-300">
                                    <tr class="font-bold text-gray-900 bg-gray-50 text-lg">
                                        <td colspan="3" class="px-6 py-4 text-right">TOTAL A PAGAR:</td>
                                        <td class="px-6 py-4 text-right text-green-700" id="total_pagar">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('ventas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium">Cancelar</a>
                            <button type="submit" id="btn_guardar" class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-bold shadow-lg disabled:opacity-50" disabled>
                                Finalizar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let totalVenta = 0;

        // 1. Al cambiar el producto, actualizamos Stock y Precio en pantalla
        document.getElementById('producto_select').addEventListener('change', function() {
            let option = this.options[this.selectedIndex];
            let stock = option.getAttribute('data-stock');
            let precio = option.getAttribute('data-precio');

            if (stock) {
                document.getElementById('stock_display').value = stock;
                document.getElementById('precio_display').value = "$" + parseFloat(precio).toFixed(2);
                document.getElementById('cantidad_input').max = stock; // Pone límite al input
            } else {
                document.getElementById('stock_display').value = 0;
                document.getElementById('precio_display').value = "$0.00";
            }
        });

        // 2. Función para agregar a la tabla
        function agregarAlCarrito() {
            let select = document.getElementById('producto_select');
            let cantidadInput = document.getElementById('cantidad_input');
            
            let id = select.value;
            let texto = select.options[select.selectedIndex].text;
            let stock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock'));
            let precio = parseFloat(select.options[select.selectedIndex].getAttribute('data-precio'));
            let cantidad = parseInt(cantidadInput.value);

            // Validaciones
            if (!id) { alert("Seleccione un producto"); return; }
            if (isNaN(cantidad) || cantidad <= 0) { alert("Cantidad inválida"); return; }
            if (cantidad > stock) { 
                alert("¡No hay suficiente stock! Solo quedan " + stock + " unidades."); 
                return; 
            }

            let subtotal = precio * cantidad;
            totalVenta += subtotal;

            // Crear fila HTML
            let fila = `
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <input type="hidden" name="productos_id[]" value="${id}">
                        ${texto}
                    </td>
                    <td class="px-6 py-4 text-right">
                        $${precio.toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="hidden" name="cantidades[]" value="${cantidad}">
                        ${cantidad}
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-700">
                        $${subtotal.toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" onclick="eliminarFila(this, ${subtotal})" class="text-red-600 hover:text-red-900 font-bold">X</button>
                    </td>
                </tr>
            `;

            document.getElementById('tabla_ventas').insertAdjacentHTML('beforeend', fila);
            actualizarTotal();

            // Resetear inputs
            select.value = "";
            document.getElementById('stock_display').value = 0;
            document.getElementById('precio_display').value = "$0.00";
            cantidadInput.value = 1;
        }

        function eliminarFila(btn, subtotal) {
            btn.closest('tr').remove();
            totalVenta -= subtotal;
            actualizarTotal();
        }

        function actualizarTotal() {
            document.getElementById('total_pagar').innerText = "$" + totalVenta.toFixed(2);
            // Habilitar botón solo si hay total > 0
            document.getElementById('btn_guardar').disabled = (totalVenta <= 0);
        }
    </script>
</x-app-layout>