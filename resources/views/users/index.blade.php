<x-app-layout>
<div class="container mt-4">
    <h2>Usuarios</h2>

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Nuevo Usuario</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->deleted_at)
                        <span class="badge bg-danger">Inactivo</span>
                    @else
                        <span class="badge bg-success">Activo</span>
                    @endif
                </td>
                <td>
                    @if(!$user->deleted_at)
                        <a href="{{ route('users.edit',$user) }}" class="btn btn-warning btn-sm">Editar</a>

                        <form action="{{ route('users.destroy',$user) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Desactivar</button>
                        </form>
                    @else
                        <a href="{{ route('users.restore',$user->id) }}" class="btn btn-info btn-sm">Restaurar</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
</div>
</x-app-layout>
