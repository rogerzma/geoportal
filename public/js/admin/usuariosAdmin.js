$(document).ready(function () {
    let usuarioAEliminarId = null;

    // 🔄 Cargar usuarios al iniciar
    cargarUsuarios();

    // 📄 Definición de funciones

    // Paginación
    let usuariosGlobal = [];
    let paginaActual = 1;
    const usuariosPorPagina = 5;

    function renderTablaUsuarios() {
        let filas = '';
        const inicio = (paginaActual - 1) * usuariosPorPagina;
        const fin = inicio + usuariosPorPagina;
        const usuariosPagina = usuariosGlobal.slice(inicio, fin);
        usuariosPagina.forEach(function (usuario) {
            filas += `
                <tr>
                    <td>${usuario.name}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.telefono || ''}</td>
                    <td>${usuario.tipo_usuario ? usuario.tipo_usuario.charAt(0).toUpperCase() + usuario.tipo_usuario.slice(1) : ''}</td>
                    <td>
                        <a href="/admin/modificar-usuario/${usuario.id}" class="glyphicon glyphicon-pencil"></a>
                        <a href="#" class="glyphicon glyphicon-trash btn-eliminar" data-id="${usuario.id}" style="color: #ff0000;"></a>
                    </td>
                </tr>
            `;
        });
        $('#tbody-up').html(filas);
        renderControlesPaginacion();
    }

    function renderControlesPaginacion() {
        let totalPaginas = Math.ceil(usuariosGlobal.length / usuariosPorPagina);
        let paginacionHtml = '<nav><ul class="pagination">';
        paginacionHtml += `<li class="page-item${paginaActual === 1 ? ' disabled' : ''}"><a class="page-link" href="#" id="anterior">&laquo;</a></li>`;
        for (let i = 1; i <= totalPaginas; i++) {
            paginacionHtml += `<li class="page-item${paginaActual === i ? ' active' : ''}"><a class="page-link page-num" href="#" data-pagina="${i}">${i}</a></li>`;
        }
        paginacionHtml += `<li class="page-item${paginaActual === totalPaginas ? ' disabled' : ''}"><a class="page-link" href="#" id="siguiente">&raquo;</a></li>`;
        paginacionHtml += '</ul></nav>';
        if ($('#paginacion-usuarios').length === 0) {
            $('#tbody-up').parent().after('<div id="paginacion-usuarios" class="text-center"></div>');
        }
        $('#paginacion-usuarios').html(paginacionHtml);

        // Eventos
        $('#anterior').off('click').on('click', function(e) {
            e.preventDefault();
            if (paginaActual > 1) {
                paginaActual--;
                renderTablaUsuarios();
            }
        });
        $('#siguiente').off('click').on('click', function(e) {
            e.preventDefault();
            let totalPaginas = Math.ceil(usuariosGlobal.length / usuariosPorPagina);
            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderTablaUsuarios();
            }
        });
        $('.page-num').off('click').on('click', function(e) {
            e.preventDefault();
            const pag = parseInt($(this).data('pagina'));
            if (pag !== paginaActual) {
                paginaActual = pag;
                renderTablaUsuarios();
            }
        });
    }

    // Cargar la lista de usuarios y renderizar la tabla
    function cargarUsuarios() {
        $.ajax({
            url: '/usuarios',
            method: 'GET',
            success: function (usuarios) {
                usuariosGlobal = usuarios;
                paginaActual = 1;
                renderTablaUsuarios();
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
            url: '/usuarios',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
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
        const userId = $("#user_id").val(); // 🔹 mejor tomar del hidden
        const name = $('#name').val();
        const telefono = $('#telefono').val();
        const email = $('#email').val();
        const tipo_usuario = $('#tipo_usuario').val();
        const password = $('#password').val();
        const password_confirmation = $('#password_confirmation').val();

        $.ajax({
            url: `/usuarios/${userId}`,
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
                window.location.href = '/admin/usuarios'; // vuelve a lista
            },
            error: function (xhr) {
                let mensaje = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errores = xhr.responseJSON.errors;
                    mensaje = Object.values(errores).flat()[0];
                }
                $('#errorMensaje').text(mensaje);
                $('#errorModal').modal('show');
            }
        });
    });

});
