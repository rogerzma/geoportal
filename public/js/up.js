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
                alert(response.message || 'Unidad de producción registrada correctamente.');
                // Limpia los campos
                $('#nombre_up').val('');
                $('#propietario').val('');
                $('#localidad').val('');
                $('#telefono').val('');
            },
            error: function(xhr) {
                console.error(xhr.responseText); // Aquí verás el error detallado en la consola del navegador
                $('#errorModal').modal('show');
            }
        });
    });
});