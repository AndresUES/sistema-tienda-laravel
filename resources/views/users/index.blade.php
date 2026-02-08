<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Usuarios
            </h2>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded hover:bg-gray-700">
                Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3 text-center">Estado</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->id }}</td>
                                        <td class="px-6 py-4 text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4">{{ $user->email }}</td>

                                        <td class="px-6 py-4 text-center">
                                            @if($user->trashed())
                                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded">Inactivo</span>
                                            @else
                                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Activo</span>
                                            @endif

                                        </td>

                                        <td class="px-6 py-4 text-center">
    <div class="flex justify-center gap-2">

        @if(!$user->trashed())
            <a href="{{ route('users.edit', $user->id) }}"
               class="inline-block bg-gray-600 text-black text-xs px-3 py-2 rounded hover:bg-gray-700">
                Editar
            </a>
        @endif

        @if($user->trashed())
            <form action="{{ route('users.restore', $user->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="bg-green-600 text-black text-xs px-3 py-2 rounded hover:bg-green-700">
                    Restaurar
                </button>
            </form>
        @else
            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 text-white text-xs px-3 py-2 rounded hover:bg-red-700">
                    Desactivar
                </button>
            </form>
        @endif

    </div>
</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-gray-500">
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>