$(document).ready(function () {
    let usuarioAEliminarId = null;

    // 🔄 Cargar usuarios al iniciar
    cargarUsuarios();

    // 📄 Definición de funciones

    // Cargar la lista de usuarios y renderizar la tabla
    function cargarUsuarios() {
        $.ajax({
            url: '/api/usuarios',
            method: 'GET',
            success: function (usuarios) {
                let filas = '';
                usuarios.forEach(function (usuario) {
                    filas += `
                        <tr>
                            <td>${usuario.name}</td>
                            <td>${usuario.email}</td>
                            <td>${usuario.telefono || ''}</td>
                            <td>${usuario.tipo_usuario ? usuario.tipo_usuario.charAt(0).toUpperCase() + usuario.tipo_usuario.slice(1) : ''}</td>
                            <td>
                                <a href="#" class="glyphicon glyphicon-pencil"></a>
                                <a href="#" class="glyphicon glyphicon-trash btn-eliminar" data-id="${usuario.id}" style="color: #ff0000;"></a>
                            </td>
                        </tr>
                    `;
                });
                $('#tbody-up').html(filas);
            },
            error: function (xhr) {
                console.error('Error al cargar los usuarios:', xhr.responseText);
                $('#tbody-up').html('<tr><td colspan="5">No se pudieron cargar los datos.</td></tr>');
            }
        });
    }

    // Mostrar el modal y capturar el ID
    $(document).on('click', '.btn-eliminar', function (e) {
        e.preventDefault();
        usuarioAEliminarId = $(this).data('id');
        $('#modalEliminarUsuario').modal('show');
    });

    // Confirmar eliminación vía AJAX
    $('#btnConfirmarEliminar').click(function () {
        if (!usuarioAEliminarId) return;

        $.ajax({
            url: `/api/usuarios/${usuarioAEliminarId}`,
            method: 'DELETE',
            success: function (response) {
                $('#modalEliminarUsuario').modal('hide');
                usuarioAEliminarId = null;
                cargarUsuarios();
            },
            error: function (xhr) {
                alert('Error al eliminar el usuario: ' + xhr.responseText);
            }
        });
    });

    // Registrar usuario nuevo
    $('#registrarUsuarioBtn').on('click', function () {
        // Obtener valores
        const name = $('#name').val();
        const telefono = $('#telefono').val();
        const email = $('#email').val();
        const tipo_usuario = $('#tipo_usuario').val();
        const password = $('#password').val();
        const password_confirmation = $('#password_confirmation').val();

        // Validar campos vacíos
        if (!name || !telefono || !email || !tipo_usuario || !password || !password_confirmation) {
            $('#emptyFieldsAlert').show();
            return;
        } else {
            $('#emptyFieldsAlert').hide();
        }

        // Validar que las contraseñas coincidan
        if (password !== password_confirmation) {
            $('#errorMensaje').text('Las contraseñas no coinciden.');
            $('#errorModal').modal('show');
            return;
        }

        // Enviar petición AJAX para crear usuario
        $.ajax({
            url: '/api/usuarios',
            method: 'POST',
            data: {
                name: name,
                telefono: telefono,
                email: email,
                tipo_usuario: tipo_usuario,
                password: password,
                password_confirmation: password_confirmation
            },
            success: function (response) {
                // Redirigir a la vista de administración de usuarios
                window.location.href = '/admin/usuarios';
            },
            error: function (xhr) {
                let mensaje = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Mostrar el primer error de validación
                    const errores = xhr.responseJSON.errors;
                    mensaje = Object.values(errores).flat()[0];
                }
                $('#errorMensaje').text(mensaje);
                $('#errorModal').modal('show');
            }
        });
    });

    // Modificar usuarios
        $('#actualizarUsuarioBtn').on('click', function () {
        const userId = "{{ $user->id }}";
        const name = $('#name').val();
        const telefono = $('#telefono').val();
        const email = $('#email').val();
        const tipo_usuario = $('#tipo_usuario').val();
        const password = $('#password').val();
        const password_confirmation = $('#password_confirmation').val();

        // Validaciones igual que antes...

        $.ajax({
            url: `/api/usuarios/${userId}`,
            method: 'PUT',
            data: {
                name,
                telefono,
                email,
                tipo_usuario,
                password,
                password_confirmation
            },
            success: function (response) {
                window.location.href = '/admin/usuarios';
            },
            error: function (xhr) {
                // Manejo de errores igual que antes...
            }
        });
    });
});
