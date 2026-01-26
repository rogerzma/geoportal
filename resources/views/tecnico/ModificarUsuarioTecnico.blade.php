@extends("layouts.appGOB")

@section("title", "ModificarUsuarioTecnico")
@section("view-name", "ModificarUsuarioTecnico")

@section("content")
<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('tecnico') }}">Técnico</a></li>
        <li class="active">Modificar usuario</li>
    </ol>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <h2>Modificar usuario</h2>
            <hr class="red">
            <p>Actualice los datos del usuario seleccionado.</p>
        </div>
        <div class="col-md-3">
            <div class="list-group">
                <a class="list-group-item" href="{{ route('tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
                <a class="list-group-item" href="{{ route('mapa-tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
                <a class="list-group-item" href="{{ route('tecnico-up') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Unidades de producción</a>
            </div>
        </div>
    </div>

    <div class="container">
        <form method="POST" action="{{ route('actualizar.usuario.tecnico', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-4">
                    <h4>Nombre del usuario</h4>
                    <input class="form-control" type="text" name="name" value="{{ $user->name }}">
                </div>
                <div class="col-md-4">
                    <h4>Teléfono</h4>
                    <input class="form-control" type="text" name="telefono" value="{{ $user->telefono }}">
                </div>
                <div class="col-md-4">
                    <h4>Correo electrónico</h4>
                    <input class="form-control" type="email" name="email" value="{{ $user->email }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h4>Tipo de usuario</h4>
                    <select class="form-control" name="tipo_usuario">
                        <option value="jefe_operativo" {{ $user->tipo_usuario == 'jefe_operativo' ? 'selected' : '' }}>Jefe operativo</option>
                        <option value="capturista" {{ $user->tipo_usuario == 'capturista' ? 'selected' : '' }}>Capturista</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <h4>Contraseña (opcional)</h4>
                    <input class="form-control" type="password" name="password" placeholder="Nueva contraseña (si aplica)">
                </div>
                <div class="col-md-4">
                    <h4>Confirmar contraseña</h4>
                    <input class="form-control" type="password" name="password_confirmation" placeholder="Repita la contraseña">
                </div>

                <div class="col-md-4" style="margin-top:2.5em;">
                    <button type="submit" class="btn btn-primary" id="registrarUsuarioBtn">Actualizar usuario</button>
                    <a href="{{ route('usuarios-tecnico') }}" class="btn btn-default">Cancelar</a>
                 </div>
            </div><br>
        </form>
    </div>
</div>

<!-- Scripts -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="{{ asset(path: 'js/tecnico/usuarioTecnico.js') }}"></script>


@endsection