@extends("layouts.appGOB")

@section("title", "CrearUP")

@section("view-name", "CrearUP")

@section("content")

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('admin') }}">Administrador</a></li>
        <li class="active">Registrar UP</li>
    </ol>
</div>

<div class="container">
    <div class="row">
      <div class="col-md-9">
          <h2>Registrar unidad de producción (UP)</h2>
          <hr class="red">
          <p>Seleccione las características de la UP a registrar.</p>
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
=======
    <div class="col-md-9">
    </div>
    <div class="col-md-3">
        <div class="list-group">
          <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
        </div>
    </div>
   </div>

    <div class="container">
      <div class="alert alert-danger" id="emptyFieldsAlert" style="display: none;">
        Campos vacíos
      </div>
      <div class="row">
          <div class="col-md-4">
            <p>
            </p></div>
            <div class="col-md-4">
              <p>
                <p><h4>Nombre del propietario</h4></p>
              </p></div>
              <div class="col-md-4">
                <p>
                    <p><h4>Localidad</h4></p>
                </p></div>
       </div>

       <div class="row">
            <div class="col-md-4">
                <p>
                    <p><h4>Telefono</h4></p>
                </p></div>
            <div class="col-md-4">
            <p>
              <p><br>
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    </div>

</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
