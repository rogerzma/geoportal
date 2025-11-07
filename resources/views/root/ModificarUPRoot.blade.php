@extends("layouts.appGOB")

@section("title", "ModificarUP")
@section("view-name", "ModificarUP")

@section("content")
<div class="container">
    <div class="container">
        <ol class="breadcrumb top-buffer">
            <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
            <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
            <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
            <li><a href="{{ route('admin') }}">Administrador</a></li>
            <li class="active">Modificar UP</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-9">
            <h2>Modificar unidad de producción (UP)</h2>
            <hr class="red">
            <p>Modifique las características de la UP.</p>
        </div>
         <div class="col-md-3">
          <div class="list-group">
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-gob') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-gob') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="list-group-item" style="text-decoration: none;"><img src="/images/templatemo_list.png" style="margin-right:10px;">Cerrar sesión</button>
            </form>
          </div>
      </div>
    </div>

    <div class="container">
        <form method="POST" action="{{ route('up.actualizar.root', $unidad->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <h4>Nombre de la UP</h4>
                    <input class="form-control" type="text" name="nombre_up" value="{{ old('nombre_up', $unidad->nombre_up) }}">
                </div>
                <div class="col-md-4">
                    <h4>Localidad</h4>
                    <input class="form-control" type="text" name="localidad" value="{{ old('localidad', $unidad->localidad) }}">
                </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                    <h4>Responsable</h4>
                    <input class="form-control" type="text" name="responsable" value="{{ old('responsable', $unidad->responsable) }}">
                </div>
                <div class="col-md-4">
                    <h4>Teléfono</h4>
                    <input class="form-control" type="text" name="telefono" value="{{ old('telefono', $unidad->telefono) }}">
                </div>
            </div>
            <div class = "row">
              <div class="col-md-4">
                    <br>
                    <button type="submit" class="btn btn-success">Actualizar UP</button>
                </div>
            </div>
        </form>
    </div><br>

    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error al crear la UP</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Por favor, complete todos los campos faltantes antes de continuar.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
