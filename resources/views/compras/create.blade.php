<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Compra</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded border">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                                <select name="proveedor_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    @foreach($proveedores as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">N° Factura</label>
                                <input type="text" name="numero_factura" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                        </div>

                        <div class="mb-6 p-4 border border-blue-100 bg-blue-50 rounded-lg">
                            <h3 class="text-sm font-bold text-blue-800 mb-2">Agregar Productos</h3>
                            
                            <div class="mb-2">
                                <label class="text-xs text-gray-600 font-bold">🔍 Buscar por Código o Nombre:</label>
                                <input type="text" id="buscador_producto" class="w-full border-blue-300 ring-2 ring-blue-100 rounded-md shadow-sm placeholder-gray-400" placeholder="Escribe aquí..." autocomplete="off">
                            </div>

                            <div class="flex flex-col md:flex-row gap-2 items-end">
                                <div class="w-full md:w-5/12">
                                    <label class="text-xs text-gray-600">Seleccionar Producto</label>
                                    <select id="select_producto" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($productos as $prod)
                                            <option value="{{ $prod->id }}" data-costo="{{ $prod->precio_compra }}">
                                                {{ $prod->codigo_barra }} - {{ $prod->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Costo ($)</label>
                                    <input type="number" id="input_costo" class="w-full border-gray-300 rounded-md shadow-sm text-sm" step="0.01">
                                </div>
                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Cantidad</label>
                                    <input type="number" id="input_cantidad" class="w-full border-gray-300 rounded-md shadow-sm text-sm" value="1">
                                </div>
                                <div class="w-full md:w-3/12">
                                    <button type="button" onclick="agregarFila()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-bold shadow">
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
                                        <th class="px-6 py-3 text-right">Costo</th>
                                        <th class="px-6 py-3 text-center">Cant.</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                        <th class="px-6 py-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_detalle"></tbody>
                                <tfoot>
                                    <tr class="font-bold text-gray-900 bg-gray-50">
                                        <td colspan="3" class="px-6 py-4 text-right">TOTAL:</td>
                                        <td class="px-6 py-4 text-right text-blue-700" id="total_pagar">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btn_guardar" class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-bold shadow-lg" disabled>
                                Guardar Compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let total = 0;
        const buscador = document.getElementById('buscador_producto');
        const selector = document.getElementById('select_producto');
        const inputCosto = document.getElementById('input_costo');
        
        // Guardamos las opciones originales al cargar la página
        const opcionesOriginales = Array.from(selector.options);

        // ------------------------------------------
        // 1. FUNCIÓN PARA ACTUALIZAR COSTO (La solución)
        // ------------------------------------------
        function actualizarCostoVisual() {
            // Verifica si hay algo seleccionado
            if (selector.selectedIndex >= 0) {
                const opcion = selector.options[selector.selectedIndex];
                const costo = opcion.getAttribute('data-costo');
                
                // Si la opción tiene costo, lo pone. Si es la de "--Seleccione--", limpia.
                if (costo) {
                    inputCosto.value = costo;
                } else {
                    inputCosto.value = '';
                }
            }
        }

        // ------------------------------------------
        // 2. BUSCADOR
        // ------------------------------------------
        buscador.addEventListener('keyup', function() {
            const texto = this.value.toLowerCase();
            
            // Limpiamos select
            selector.innerHTML = '';

            // Filtramos
            const filtradas = opcionesOriginales.filter(op => {
                return op.text.toLowerCase().includes(texto);
            });

            // Agregamos las filtradas
            filtradas.forEach(op => selector.add(op));

            // Si no hay resultados exactos o está vacío el buscador, restauramos todo
            if (filtradas.length === 0 || texto === '') {
                selector.innerHTML = '';
                opcionesOriginales.forEach(op => selector.add(op));
            }

            // IMPORTANTE: Forzamos la selección del primero y actualizamos precio
            if (selector.options.length > 0) {
                selector.selectedIndex = 0; 
                actualizarCostoVisual(); // <--- ESTA LÍNEA ARREGLA TU ERROR
            }
        });

        // ------------------------------------------
        // 3. EVENTO DE CAMBIO MANUAL
        // ------------------------------------------
        selector.addEventListener('change', function() {
            actualizarCostoVisual();
        });

        // ------------------------------------------
        // 4. AGREGAR A LA TABLA
        // ------------------------------------------
        function agregarFila() {
            const id = selector.value;
            const texto = selector.options[selector.selectedIndex].text;
            const costo = parseFloat(inputCosto.value);
            const cantidad = parseInt(document.getElementById('input_cantidad').value);

            if(!id || !costo || !cantidad) {
                Alerta.show("Verifique que seleccionó producto y tiene costo/cantidad");
                return;
            }

            const subtotal = costo * cantidad;
            total += subtotal;

            const fila = `
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <input type="hidden" name="productos_id[]" value="${id}">
                        ${texto}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <input type="hidden" name="precios[]" value="${costo}">
                        $${costo.toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="hidden" name="cantidades[]" value="${cantidad}">
                        ${cantidad}
                    </td>
                    <td class="px-6 py-4 text-right font-bold">
                        $${subtotal.toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" onclick="eliminar(this, ${subtotal})" class="text-red-600 font-bold hover:underline">X</button>
                    </td>
                </tr>
            `;

            document.getElementById('tabla_detalle').insertAdjacentHTML('beforeend', fila);
            actualizarTotal();
            
            // Limpiar inputs para el siguiente
            selector.value = "";
            inputCosto.value = "";
            document.getElementById('input_cantidad').value = 1;
            buscador.value = "";
            
            // Restaurar lista completa
            selector.innerHTML = ''; 
            opcionesOriginales.forEach(op => selector.add(op));
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