$(document).ready(function() {
    // Configurar CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#validateReportButton').on('click', function() {
        // Obtener valores
        const nombre_up = $('#nombre_up').val();
        const propietario = $('#propietario').val();
        const localidad = $('#localidad').val();
        const telefono = $('#telefono').val();
        const user_id = $('#user_id').val();
        const responsable_tecnico = $('#responsable_tecnico').val();

        // Validar campos vacíos
        if (!nombre_up || !propietario || !localidad || !telefono) {
            $('#emptyFieldsAlert').show();
            return;
        } else {
            $('#emptyFieldsAlert').hide();
        }

        // Enviar petición AJAX
        $.ajax({
            url: '/api/unidades-produccion',
            method: 'POST',
            data: {
                nombre_up: nombre_up,
                propietario: propietario,
                localidad: localidad,
                telefono: telefono,
                user_id: user_id,
                responsable_tecnico: responsable_tecnico
            },
            success: function(response) {
                window.location.href = urlUnidadesProduccion;
            },
            error: function(xhr) {
                console.error(xhr.responseText); // Aquí verás el error detallado en la consola del navegador
                $('#errorModal').modal('show');
            }
        });
    });
});

$(document).ready(function() {
    // Cargar unidades de producción al cargar la página
    cargarUnidadesProduccion();

    function cargarUnidadesProduccion() {
        $.ajax({
            url: '/api/unidades-produccion',
            method: 'GET',
            success: function(response) {
                let filas = '';
                response.forEach(function(up) {
                    filas += `
                        <tr>
                            <td>${up.nombre_up}</td>
                            <td>${up.propietario}</td>
                            <td>${up.localidad}</td>
                            <td>${up.telefono}</td>
                            <td>
                                <a href="#" class="glyphicon glyphicon-pencil"></a>
                                <a href="#" class="glyphicon glyphicon-trash" style="color: #ff0000;"></a>
                            </td>
                        </tr>
                    `;
                });
                $('#tbody-up').html(filas);
            },
            error: function(xhr) {
                console.error('Error al cargar las unidades de producción:', xhr.responseText);
                $('#tbody-up').html('<tr><td colspan="5">No se pudieron cargar los datos.</td></tr>');
            }
        });
    }
});