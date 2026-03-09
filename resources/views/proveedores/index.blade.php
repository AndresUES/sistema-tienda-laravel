<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Proveedores') }}
            </h2>
            <a href="{{ route('proveedores.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 text-white transition ease-in-out duration-150">
                Nuevo Proveedor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Empresa / Nombre</th>
                                    <th class="px-6 py-3">NIT / NRC</th>
                                    <th class="px-6 py-3">Contacto</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($proveedores as $proveedor)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $proveedor->id }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ $proveedor->nombre }}
                                        <div class="text-xs text-gray-500 font-normal">{{Str::limit($proveedor->direccion, 30)}}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">NIT: {{ $proveedor->nit }}</div>
                                        <div class="text-xs text-gray-500">NRC: {{ $proveedor->nrc }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>📞 {{ $proveedor->telefono }}</div>
                                        <div class="text-xs text-blue-600">{{ $proveedor->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2">
                                                Editar
                                            </a>
                                            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" onsubmit="return confirm('¿Eliminar a {{ $proveedor->nombre }}?');" class="inline-block m-0 p-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-2">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay proveedores registrados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $proveedores->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>