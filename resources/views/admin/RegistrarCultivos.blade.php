@extends("layouts.appGOB")

@section("title", "RegistrarCultivo")

@section("view-name", "RegistrarCultivo")

@section("content")

<style>
  .fila-variante{
      display:flex;
      align-items:center;
      margin-bottom:12px;
  }

  .fila-variante .input-variante{
      flex:1;
  }

  .btn-agregar-variante,
  .btn-quitar-variante{
      margin-left:8px;
      width:38px;
      height:38px;

      display:flex;
      align-items:center;
      justify-content:center;

      padding:0;
  }

  .btn-agregar-variante .glyphicon,
  .btn-quitar-variante .glyphicon{
      font-size:14px;
  }        
</style>

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('admin') }}">Administrador</a></li>
        <li class="active">Registrar Cultivo</li>
    </ol>
</div>

<div class="container">
    <div class="row">
      <div class="col-md-9">
          <h2>Registrar cultivo</h2>
          <hr class="red">
          <p>Seleccione las características del cultivo a registrar.</p>
      </div>
      <div class="col-md-3">
          <div class="list-group">
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('administrar-usuarios-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a>
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
              <p><h4>Nombre del cultivo</h4></p>
              <input class="form-control" placeholder="Ingrese el nombre del cultivo" type="text" name="nombre" id="nombre_cultivo">
            </p></div>
              <div class="col-md-4">
                <p>
                    <p><h4>Nombre científico</h4></p>
                    <input class="form-control" placeholder="Ingrese el nombre científico del cultivo" type="text" name="nombre_cientifico" id="nombre_cientifico">
                </p></div>
              <div class="col-md-4">
                <p>
                    <p><h4>Categoría</h4></p>
                    <select class="form-control" name="categoria" id="categoria" required>
                        <option disabled selected>Seleccione...</option>
                        <option value="Cereal">Cereal</option>
                        <option value="Leguminosa">Leguminosa</option>
                        <option value="Hortaliza">Hortaliza</option>
                        <option value="Frutal">Frutal</option>
                        <option value="Oleaginosa">Oleaginosa</option>
                        <option value="Tuberculo">Tubérculo</option>
                        <option value="Forrajero">Forrajero</option>
                        <option value="Forestal">Forestal</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Otra">Otra</option>
                    </select>
                </p></div>
       </div>

       <div class="row">
            <div class="col-md-4">
            <h4>Color</h4>
            <div class="input-group color-selector-group">
                <span class="input-group-addon color-preview-addon">
                    <input
                        type="color"
                        id="color_picker"
                        value="#009933"
                        title="Seleccione un color"
                    >
                </span>

                <input
                    type="text"
                    class="form-control"
                    name="color"
                    id="color"
                    value="#009933"
                    maxlength="7"
                    placeholder="#009933"
                >
            </div></div>
            <div class="col-md-4">
                <p>
                    <p><h4>¿Activo?</h4></p>
                    <select class="form-control" name="activo" id="activo" required>
                        <option disabled selected>Seleccione...</option>
                      <option value="1">Sí</option>
                      <option value="0">No</option>
                    </select>
                </p></div>
      </div>
    
      <div class="row">
        <div class="col-md-8">
            <h4>¿El cultivo tiene variantes?</h4>

            <label class="radio-inline">
                <input
                    type="radio"
                    name="tiene_variantes"
                    id="variante_si"
                    value="si"
                >
                Sí
            </label>

            <label class="radio-inline">
                <input
                    type="radio"
                    name="tiene_variantes"
                    id="variante_no"
                    value="no"
                >
                No
            </label>
        </div>
    </div>

    <div
        class="row"
        id="contenedor-variantes"
        style="display: none; margin-top: 20px;"
    >
        <div class="col-md-5">
            <h4>Variantes</h4>

            <div id="lista-variantes"></div>
        </div>
    </div>

     
     <div class="row">
        <div class="col-md-4">
            <p>
              <p><br>
                <button type="button" class="btn btn-primary" id="validateReportButton">
                    Registrar cultivo
                </button>
              </p>
            </p></div>
            <input type="hidden" id="user_id" value="{{ Auth::id() }}">
     </div>
     <br>

    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error al crear el cultivo</h5>
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
    const urlCultivosStore = "{{ route('root.cultivos.store') }}";
    const urlCultivosRedirect = "{{ route('administrar-cultivos-root') }}";
</script>
<script src="{{ asset('js/root/cultivos/cultivoRoot.js') }}"></script>




@endsection