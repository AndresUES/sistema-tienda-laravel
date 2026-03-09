<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Cartera de Clientes') }}</h2>
            <a href="{{ route('clientes.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase font-bold hover:bg-gray-700">Nuevo Cliente</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Documentos (DUI/NIT/NRC)</th>
                                    <th class="px-6 py-3">Contacto</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientes as $cliente)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $cliente->nombre }}</td>
                                    <td class="px-6 py-4">
                                        @if($cliente->dui) <div class="text-xs"><span class="font-bold">DUI:</span> {{ $cliente->dui }}</div> @endif
                                        @if($cliente->nit) <div class="text-xs"><span class="font-bold">NIT:</span> {{ $cliente->nit }}</div> @endif
                                        @if($cliente->nrc) <div class="text-xs text-blue-600"><span class="font-bold">NRC:</span> {{ $cliente->nrc }}</div> @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>📞 {{ $cliente->telefono ?? '-' }}</div>
                                        <div class="text-xs">{{ $cliente->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-xs px-3 py-2">Editar</a>
                                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Eliminar cliente?');" class="inline-block m-0 p-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-xs px-3 py-2">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4">No hay clientes registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $clientes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>