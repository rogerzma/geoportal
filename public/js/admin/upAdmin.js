$(document).ready(function () {
    // 🛡 CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log('Rol del usuario:', window.userRole);

    // 🧩 Crear nueva UP
    $('#validateReportButton').on('click', function () {
        const nombre_up = $('#nombre_up').val();
        const responsable = $('#responsable').val();
        const localidad = $('#localidad').val();
        const telefono = $('#telefono').val();
        const user_id = $('#user_id').val();
        const responsable_tecnico = $('#responsable_tecnico').val();

        if (!nombre_up || !responsable || !localidad || !telefono) {
            $('#emptyFieldsAlert').show();
            return;
        }
        $('#emptyFieldsAlert').hide();

        $.ajax({
            url: '/api/unidades-produccion',
            method: 'POST',
            data: {
                nombre_up,
                responsable,
                localidad,
                telefono,
                user_id,
                responsable_tecnico
            },
            success: function (response) {
                window.location.href = urlUnidadesProduccion;
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#errorModal').modal('show');
            }
        });
    });

    // 📦 Cargar tabla de UP
        function cargarUnidadesProduccion() {
        $.ajax({
            url: '/api/unidades-produccion',
            method: 'GET',
            success: function (response) {
                if (response.length === 0) {
                    $('#tabla-up-wrapper').hide();
                    $('#mensaje-vacio').show();
                } else {
                    $('#tabla-up-wrapper').show();
                    $('#mensaje-vacio').hide();

                    let filas = '';
                    response.forEach(function (up) {

                        filas += `
                            <tr>
                                <td>${up.nombre_up}</td>
                                <td>${up.localidad}</td>
                                <td>${up.responsable}</td>
                                <td>${up.telefono}</td>
                                <td>
                                    <a href="/admin/up/poligonos?up_id=${up.id}" title="Ver mapa">
                                        <img src="/images/map-icon.png" width="20" height="20" alt="Mapa">
                                    </a>
                                </td>
                                <td>
                                    <a href="/admin/modificar-up/${up.id}" class="glyphicon glyphicon-pencil"></a>
                                    <a href="#" class="glyphicon glyphicon-trash btn-eliminar-up" data-id="${up.id}" style="color: #ff0000;"></a>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tbody-up').html(filas);
                }
            },
            error: function (xhr) {
                console.error('Error al cargar las unidades de producción:', xhr.responseText);
                $('#tabla-up-wrapper').hide();
                $('#mensaje-vacio').show().html('<div class="alert alert-danger">No se pudieron cargar los datos.</div>');
            }
        });
    }

    cargarUnidadesProduccion();

    // 🗑️ Flujo para eliminar UP con modal de confirmación
    let upIdParaEliminar = null;

    $(document).on('click', '.btn-eliminar-up', function (e) {
        e.preventDefault();
        upIdParaEliminar = $(this).data('id'); // ← correcto
        $('#modalEliminarUP').modal('show');
    });

    $('#btnConfirmarEliminar').on('click', function () {
        if (!upIdParaEliminar) return;

        $.ajax({
            url: `/api/unidades-produccion/${upIdParaEliminar}`,
            method: 'DELETE',
            success: function (response) {
                $('#modalEliminarUP').modal('hide');
                upIdParaEliminar = null;
                cargarUnidadesProduccion();
            },
            error: function (xhr) {
                alert('Error al eliminar la unidad de producción: ' + xhr.responseText);
            }
        });
    });
});
