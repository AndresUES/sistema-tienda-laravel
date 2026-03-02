<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nueva Compra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('compras.store') }}" method="POST" id="form_compra">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg border">
                            <div>
                                <x-input-label for="proveedor_id" :value="__('Proveedor')" />
                                <select name="proveedor_id" id="proveedor_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="">Seleccione un proveedor</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="numero_factura" :value="__('N° Factura / Documento')" />
                                <x-text-input id="numero_factura" class="block mt-1 w-full" type="text" name="numero_factura" required />
                            </div>

                            <div>
                                <x-input-label for="fecha" :value="__('Fecha de Compra')" />
                                <x-text-input id="fecha" class="block mt-1 w-full" type="date" name="fecha" value="{{ date('Y-m-d') }}" required />
                            </div>
                        </div>

                        <div class="mb-6 p-4 border border-blue-100 bg-blue-50 rounded-lg">
                            <h3 class="text-sm font-bold text-blue-800 mb-2">Detalle de la Compra</h3>
                            
                            <div class="flex flex-col md:flex-row gap-2 items-end">
                                <div class="w-full md:w-5/12">
                                    <label class="text-xs text-gray-600">Buscar Producto</label>
                                    <select id="producto_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                        <option value="">Seleccione...</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}">
                                                {{ $producto->codigo_barra }} - {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Costo Unitario ($)</label>
                                    <input type="number" id="precio_compra" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm" step="0.01">
                                </div>

                                <div class="w-full md:w-2/12">
                                    <label class="text-xs text-gray-600">Cantidad</label>
                                    <input type="number" id="cantidad" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm" value="1" min="1">
                                </div>

                                <div class="w-full md:w-3/12">
                                    <button type="button" onclick="agregarDetalle()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-bold shadow">
                                        + Agregar Fila
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto mb-6 border rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Producto</th>
                                        <th class="px-6 py-3 text-right">Costo Unit.</th>
                                        <th class="px-6 py-3 text-center">Cant.</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                        <th class="px-6 py-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_detalle">
                                    </tbody>
                                <tfoot class="border-t-2 border-gray-300">
                                    <tr class="font-bold text-gray-900 bg-gray-50 text-base">
                                        <td colspan="3" class="px-6 py-4 text-right">TOTAL A PAGAR:</td>
                                        <td class="px-6 py-4 text-right text-blue-700" id="total_pagar">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('compras.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium">
                                Cancelar
                            </a>
                            <button type="submit" id="btn_guardar" class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 font-bold shadow-lg disabled:opacity-50" disabled>
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
        let indice = 0; // Para generar IDs únicos si fuera necesario

        function agregarDetalle() {
            // 1. Obtener valores
            let producto_id = document.getElementById('producto_id').value;
            let producto_texto = document.getElementById('producto_id').options[document.getElementById('producto_id').selectedIndex].text;
            let precio = document.getElementById('precio_compra').value;
            let cantidad = document.getElementById('cantidad').value;

            // 2. Validaciones
            if (producto_id == "") { alert("Seleccione un producto"); return; }
            if (precio == "" || precio <= 0) { alert("Ingrese un costo válido"); return; }
            if (cantidad == "" || cantidad <= 0) { alert("Ingrese una cantidad válida"); return; }

            // 3. Cálculos
            let subtotal = parseFloat(precio) * parseInt(cantidad);
            total += subtotal;

            // 4. Crear fila HTML
            // Nota los nombres de los inputs: productos_id[], precios[], cantidades[]
            // Estos arrays son los que recibe el Controlador.
            let fila = `
                <tr class="bg-white border-b hover:bg-gray-50" id="fila${indice}">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <input type="hidden" name="productos_id[]" value="${producto_id}">
                        ${producto_texto}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <input type="hidden" name="precios[]" value="${precio}">
                        $${parseFloat(precio).toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="hidden" name="cantidades[]" value="${cantidad}">
                        ${cantidad}
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-700">
                        $${subtotal.toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" class="text-red-600 hover:text-red-900 font-bold" onclick="eliminarFila(${indice}, ${subtotal})">X</button>
                    </td>
                </tr>
            `;

            // 5. Insertar en tabla
            document.getElementById('tabla_detalle').insertAdjacentHTML('beforeend', fila);
            
            // 6. Actualizar Totales y limpiar campos
            actualizarTotal();
            limpiarCampos();
            indice++;
        }

        function eliminarFila(index, subtotal) {
            document.getElementById('fila' + index).remove();
            total -= subtotal;
            actualizarTotal();
        }

        function actualizarTotal() {
            document.getElementById('total_pagar').innerHTML = "$" + total.toFixed(2);
            
            // Habilitar o deshabilitar botón guardar
            if (total > 0) {
                document.getElementById('btn_guardar').disabled = false;
            } else {
                document.getElementById('btn_guardar').disabled = true;
            }
        }

        function limpiarCampos() {
            document.getElementById('producto_id').value = "";
            document.getElementById('precio_compra').value = "";
            document.getElementById('cantidad').value = "1";
        }

        // Auto-rellenar precio al seleccionar producto
        document.getElementById('producto_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let precio = selectedOption.getAttribute('data-precio');
            if(precio) {
                document.getElementById('precio_compra').value = precio;
            }
        });
    </script>
</x-app-layout>