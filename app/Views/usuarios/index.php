<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Usuarios<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Administración de Usuarios<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Usuarios</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalCrear()">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaUsuarios" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">ID</th>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th class="text-center">Rol</th>
                        <th class="text-center">Estado</th>
                        <th style="width: 120px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalTitle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUsuario" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_usuario" name="id_usuario">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Juan Pérez">
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@dominio.com">
                        <div class="invalid-feedback" id="error-correo"></div>
                    </div>

                    <div class="mb-3">
                        <label for="clave" class="form-label">Contraseña <span id="clave-requerida" class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="clave" name="clave" placeholder="••••••••">
                        <small id="clave-help" class="form-text text-muted d-none">Deje en blanco si no desea cambiar la contraseña actual.</small>
                        <div class="invalid-feedback" id="error-clave"></div>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol del Sistema <span class="text-danger">*</span></label>
                        <select class="form-select" id="rol" name="rol">
                            <option value="">-- Seleccione un Rol --</option>
                            <option value="administrador">Administrador</option>
                            <option value="encargado">Encargado</option>
                        </select>
                        <div class="invalid-feedback" id="error-rol"></div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        <div class="invalid-feedback" id="error-estado"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let tablaUsuarios;
    const modalElement = document.getElementById('modalUsuario');
    const modalBS = new bootstrap.Modal(modalElement);

    $(document).ready(function() {
        $('#modalUsuario').on('shown.bs.modal', function() {
            $('#nombre').trigger('focus');
        });

        tablaUsuarios = $('#tablaUsuarios').DataTable({
            "ajax": "<?= base_url('usuarios/getUsuarios') ?>",
            "columns": [
                { "data": "id_usuario" },
                { "data": "nombre" },
                { "data": "correo" },
                { 
                    "data": "rol",
                    "className": "text-center",
                    "render": function(data) {
                        return data === 'administrador' 
                            ? '<span class="badge bg-primary">Administrador</span>' 
                            : '<span class="badge bg-info text-dark">Encargado</span>';
                    }
                },
                { 
                    "data": "estado",
                    "className": "text-center",
                    "render": function(data) {
                        return data == 1 
                            ? '<span class="badge bg-success">Activo</span>' 
                            : '<span class="badge bg-danger">Inactivo</span>';
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm me-1" onclick="editarUsuario(${row.id_usuario})" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${row.id_usuario})" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            "language": typeof dtLanguageEs !== 'undefined' ? dtLanguageEs : {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
            }
        });

        $('#formUsuario').on('submit', function(e) {
            e.preventDefault();
            limpiarErrores();

            $.ajax({
                url: "<?= base_url('usuarios/guardar') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        modalBS.hide();
                        tablaUsuarios.ajax.reload();
                        Toast.fire({ icon: 'success', title: response.message });
                    } else if (response.status === 'error') {
                        mostrarErrores(response.errors);
                    }
                }
            });
        });
    });

    function abrirModalCrear() {
        $('#formUsuario')[0].reset();
        $('#id_usuario').val('');
        limpiarErrores();

        $('#clave-requerida').removeClass('d-none');
        $('#clave-help').addClass('d-none');

        $('#modalTitle').text('Nuevo Usuario');
        modalBS.show();
    }

    function editarUsuario(id) {
        limpiarErrores();
        $.get("<?= base_url('usuarios/obtener/') ?>" + id, function(response) {
            if (response.status === 'success') {
                $('#id_usuario').val(response.data.id_usuario);
                $('#nombre').val(response.data.nombre);
                $('#correo').val(response.data.correo);
                $('#clave').val(''); // Vacío por defecto
                $('#rol').val(response.data.rol);
                $('#estado').val(response.data.estado);

                $('#clave-requerida').addClass('d-none');
                $('#clave-help').removeClass('d-none');

                $('#modalTitle').text('Editar Usuario');
                modalBS.show();
            } else {
                Toast.fire({ icon: 'error', title: response.message });
            }
        });
    }

    function eliminarUsuario(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('usuarios/eliminar/') ?>" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status === 'success') {
                            tablaUsuarios.ajax.reload();
                            Toast.fire({ icon: 'success', title: response.message });
                        } else {
                            Toast.fire({ icon: 'error', title: response.message });
                        }
                    }
                });
            }
        });
    }

    function mostrarErrores(errors) {
        if (!errors) return;
        
        let primerFocus = false;
        const campos = ['nombre', 'correo', 'clave', 'rol', 'estado'];

        campos.forEach(campo => {
            if (errors[campo]) {
                $(`#${campo}`).addClass('is-invalid');
                $(`#error-${campo}`).text(errors[campo]);
                if (!primerFocus) {
                    $(`#${campo}`).trigger('focus');
                    primerFocus = true;
                }
            }
        });
    }

    function limpiarErrores() {
        ['nombre', 'correo', 'clave', 'rol', 'estado'].forEach(campo => {
            $(`#${campo}`).removeClass('is-invalid');
            $(`#error-${campo}`).text('');
        });
    }
</script>
<?= $this->endSection() ?>