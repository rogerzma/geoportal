@extends("layouts.appGOB")

@section("title", "ModificarUsuarioRoot")

@section("view-name", "ModificarUsuarioRoot")

@section("content")

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('root') }}">Superusuario</a></li>
        <li class="active">Registrar usuario</li>
    </ol>
</div>

<div class="container">
    <div class="row">
    <div class="col-md-9">
        <h2>Registrar un nuevo usuario</h2>
        <hr class="red">
        <p>Ingrese los datos del usuario a registrar.</p>
    </div>
    <div class="col-md-3">
        <div class="list-group">
          <a class="list-group-item" style="text-decoration: none;" href="{{ route('root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
          <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
          <a class="list-group-item" style="text-decoration: none;" href="{{ route('unidades-produccion-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Unidades de producción</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="list-group-item" style="text-decoration: none;"><img src="/images/templatemo_list.png" style="margin-right:10px;">Cerrar sesión</button>
          </form>
        </div>
    </div>
    </div>

    <div class="container">
        <div class="alert alert-danger" id="emptyFieldsAlert" style="display: none;">
            Campos vacíos
        </div>
        <div class="row">
            <div class="col-md-4">
                <h4>Nombre del usuario</h4>
                <input class="form-control" type="text" name="name" id="name" value="{{ $user->name }}">
            </div>

            <div class="col-md-4">
                <h4>Teléfono</h4>
                <input class="form-control" type="text" name="telefono" id="telefono" value="{{ $user->telefono }}">
            </div>

            <div class="col-md-4">
                <h4>Correo electrónico</h4>
                <input class="form-control" type="email" name="email" id="email" value="{{ $user->email }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <h4>Tipo de usuario</h4>
                <select class="form-control" name="tipo_usuario" id="tipo_usuario">
                    <option value="jefe_operativo" {{ $user->tipo_usuario == 'jefe_operativo' ? 'selected' : '' }}>Jefe operativo</option>
                    <option value="capturista" {{ $user->tipo_usuario == 'capturista' ? 'selected' : '' }}>Capturista</option>
                </select>
            </div>

            <div class="col-md-4">
                <h4>Contraseña (opcional)</h4>
                <input class="form-control" type="password" name="password" id="password" placeholder="Nueva contraseña (si aplica)">
            </div>

            <div class="col-md-4">
                <h4>Confirmar contraseña</h4>
                <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" placeholder="Repita la contraseña">
            </div>

            <div class="col-md-4" style="margin-top:2.5em;">
                <button type="button" class="btn btn-success" id="actualizarUsuarioBtn">Actualizar usuario</button>
                <a href="{{ route('administrar-usuarios-root') }}" class="btn btn-default">Cancelar</a>
            </div>
        </div><br>

        <input type="hidden" id="user_id" value="{{ Auth::id() }}">
        <input type="hidden" id="responsable_tecnico" value="{{ Auth::user()->name }}">
        </div>

        <!-- Modal de error -->
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error al crear el usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="errorMensaje">Ocurrió un error inesperado.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
        </div>

<!-- Scripts -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="{{ asset('js/tecnico/usuarioTecnico.js') }}"></script>


@endsection