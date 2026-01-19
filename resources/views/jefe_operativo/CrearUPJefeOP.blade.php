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
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('usuarios-tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a>
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
            <p>
              <p><h4>Nombre de la UP</h4></p>
              <input class="form-control" placeholder="Ingrese el nombre de la UP" type="text" name="nombre" id="nombre_up">
            </p></div>
              <div class="col-md-4">
                <p>
                    <p><h4>Localidad</h4></p>
                    <input class="form-control" placeholder="Localidad donde se ubica la UP" type="text" name="localidad" id="localidad">
                </p></div>
                <div class="col-md-4">
                  <p>
                      <p><h4>Nombre del capturista</h4></p>
                      <select class="form-control" id="capturista">
                          <option value="">No aplica</option>
                          @foreach($capturistas as $capturista)
                              <option value="{{ $capturista->id }}">{{ $capturista->name }}</option>
                          @endforeach
                      </select>
                  </p>
                </div>
       </div>

       <div class="row">
            <div class="col-md-4">
              <p>
                <p><h4>Responsable de la UP</h4></p>
                <input class="form-control" placeholder="Ingrese el nombre del responsable" type="text" name="responsable" id="responsable">
              </p></div>
            <div class="col-md-4">
                <p>
                    <p><h4>Telefono</h4></p>
                    <input class="form-control" placeholder="Localidad donde se ubica la UP" type="text" name="telefono" id="telefono">
                </p></div>

     </div>
     
     <div class="row">
        <div class="col-md-4">
            <p>
              <p><br>
                <button type="button" class="btn btn-primary" id="validateReportButton">Registrar UP</button>
              </p>
            </p></div>
            <input type="hidden" id="user_id" value="{{ Auth::id() }}">
     </div>
     <br>

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

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const urlUnidadesProduccion = "{{ route('jefe-operativo-up') }}";
</script>
<script src="{{ asset('js/jefe_operativo/upJefeOperativo.js') }}"></script>




@endsection