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
        <li class="active">Registrar rancho</li>
    </ol>
</div>

<div class="container">
    <div class="row">
    <div class="col-md-9">
        <h2>Registrar rancho</h2>
        <hr class="red">
        <p>Seleccione las características del rancho a registrar.</p>
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
              <p><h4>Nombre del rancho</h4></p>
              <input class="form-control" placeholder="Ingrese el nombre del rancho" type="text" name="nombre">
            </p></div>
            <div class="col-md-4">
              <p>
                <p><h4>Nombre del propietario</h4></p>
                <input class="form-control" placeholder="Ingrese el nombre del propietario" type="text" name="propietario">
              </p></div>
              <div class="col-md-4">
                <p>
                    <p><h4>Localidad</h4></p>
                    <input class="form-control" placeholder="Localidad donde se ubica el rancho" type="text" name="localidad">
                </p></div>
       </div>

       <div class="row">
            <div class="col-md-4">
                <p>
                    <p><h4>Telefono</h4></p>
                    <input class="form-control" placeholder="Localidad donde se ubica el rancho" type="text" name="telefono">
                </p></div>
            <div class="col-md-4">
            <p>
              <p><br>
                <button type="button" class="btn btn-primary" id="validateReportButton">Registrar rancho</button>
              </p>
            </p></div>

     </div>

    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error al subir el reporte</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Por favor, complete todos los campos faltantes antes de subir el reporte.</p>
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
    $(document).ready(function () {
      $('#validateReportButton').click(function (e) {
        e.preventDefault(); // Evita que el formulario se envíe automáticamente

        if (validateForm()) {
          // Si el formulario es válido, continúa con el envío del formulario
          $('#reportForm').submit(); // Envía el formulario
        } else {
          $('#errorModal').modal('show'); // Muestra la ventana emergente de error
        }
      });

      // Función para validar el formulario
      function validateForm() {
        // Realiza la validación del formulario aquí
        var nombreCultivo = document.querySelector('[name="nombrecultivo"]').value;
        var nombreCientifico = document.querySelector('[name="nombrecientifico"]').value;
        var tipoCultivo = document.querySelector('[name="tipocultivo"]').value;
        var modalidad = document.querySelector('[name="modalidad"]').value;
        var cicloCultivo = document.querySelector('[name="ciclocultivo"]').value;
        var potAlto = document.querySelector('[name="potencialalto"]').value;
        var potMedio = document.querySelector('[name="potencialmedio"]').value;
        var potBajo = document.querySelector('[name="potencialbajo"]').value;
        var pdf = document.querySelector('[name="pdf"]').value;

        // Agrega más validaciones según tus necesidades

        // Verifica si algún campo obligatorio está vacío
        if (nombreCultivo === ''
        || nombreCientifico === ''
        || tipoCultivo === ''
        || modalidad === ''
        || cicloCultivo === ''
        || potAlto === ''
        || potMedio === ''
        || potBajo === ''
        || pdf === '') {
          return false; // Devuelve falso si hay campos vacíos
        }
        return true;
      }
    });
</script>



@endsection
