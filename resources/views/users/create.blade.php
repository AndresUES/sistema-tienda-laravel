<x-app-layout>
<div class="container mt-4">
    <h2>Nuevo Usuario</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Nombre" class="form-control mb-2">
        <input type="email" name="email" placeholder="Email" class="form-control mb-2">
        <input type="password" name="password" placeholder="Password" class="form-control mb-2">
        <input type="password" name="password_confirmation" placeholder="Confirmar" class="form-control mb-2">

        <button class="btn btn-success">Guardar</button>
    </form>
</div>
</x-app-layout>
