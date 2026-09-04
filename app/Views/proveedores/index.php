<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Proveedores<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Administración de Proveedores<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Proveedores</h3>
        <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="abrirModalCrear()">
            <i class="bi bi-truck"></i> Nuevo Proveedor
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaProveedores" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">ID</th>
                        <th>Identificación (RUC / Cédula)</th>
                        <th>Nombre / Razón Social</th>
                        <th>Teléfono</th>
                        <th style="width: 120px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar / Editar -->
<div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalTitle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formProveedor" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_proveedor" name="id_proveedor">

                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Identificación (Cédula o RUC) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" maxlength="13" placeholder="Ej. 1003456789 o 1003456789001">
                        <div class="invalid-feedback" id="error-identificacion"></div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre / Razón Social <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Distribuidora del Norte S.A.">
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej. 0991234567">
                        <div class="invalid-feedback" id="error-telefono"></div>
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
    let tablaProveedores;
    const modalElement = document.getElementById('modalProveedor');
    const modalBS = new bootstrap.Modal(modalElement);

    $(document).ready(function() {
        $('#modalProveedor').on('shown.bs.modal', function() {
            $('#identificacion').trigger('focus');
        });

        // Restringir ingreso a solo números
        $('#identificacion').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        tablaProveedores = $('#tablaProveedores').DataTable({
            "ajax": "<?= base_url('proveedores/getProveedores') ?>",
            "columns": [
                { "data": "id_proveedor" },
                { "data": "identificacion" },
                { "data": "nombre" },
                { "data": "telefono", "render": function(data) { return data ? data : '<span class="text-muted">N/A</span>'; } },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm me-1" onclick="editarProveedor(${row.id_proveedor})" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarProveedor(${row.id_proveedor})" title="Eliminar">
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

        $('#formProveedor').on('submit', function(e) {
            e.preventDefault();
            limpiarErrores();

            $.ajax({
                url: "<?= base_url('proveedores/guardar') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        modalBS.hide();
                        tablaProveedores.ajax.reload();
                        Toast.fire({ icon: 'success', title: response.message });
                    } else if (response.status === 'error') {
                        mostrarErrores(response.errors);
                    }
                }
            });
        });
    });

    function abrirModalCrear() {
        $('#formProveedor')[0].reset();
        $('#id_proveedor').val('');
        limpiarErrores();
        $('#modalTitle').text('Nuevo Proveedor');
        modalBS.show();
    }

    function editarProveedor(id) {
        limpiarErrores();
        $.get("<?= base_url('proveedores/obtener/') ?>" + id, function(response) {
            if (response.status === 'success') {
                $('#id_proveedor').val(response.data.id_proveedor);
                $('#identificacion').val(response.data.identificacion);
                $('#nombre').val(response.data.nombre);
                $('#telefono').val(response.data.telefono);

                $('#modalTitle').text('Editar Proveedor');
                modalBS.show();
            } else {
                Toast.fire({ icon: 'error', title: response.message });
            }
        });
    }

    function eliminarProveedor(id) {
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
                    url: "<?= base_url('proveedores/eliminar/') ?>" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status === 'success') {
                            tablaProveedores.ajax.reload();
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
        const campos = ['identificacion', 'nombre', 'telefono'];
        
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
        ['identificacion', 'nombre', 'telefono'].forEach(campo => {
            $(`#${campo}`).removeClass('is-invalid');
            $(`#error-${campo}`).text('');
        });
    }
</script>
<?= $this->endSection() ?>