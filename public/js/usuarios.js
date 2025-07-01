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

    // Aquí podrías agregar funciones futuras (crear usuarios, edición, filtros, etc.)
});
