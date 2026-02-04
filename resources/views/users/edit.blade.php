<x-app-layout>
<div class="container mt-4">
    <h2>Editar Usuario</h2>

    <form action="{{ route('users.update',$user) }}" method="POST">
        @csrf @method('PUT')

        <input type="text" name="name" value="{{ $user->name }}" class="form-control mb-2">
        <input type="email" name="email" value="{{ $user->email }}" class="form-control mb-2">

        <button class="btn btn-success">Actualizar</button>
    </form>
</div>
</x-app-layout>
