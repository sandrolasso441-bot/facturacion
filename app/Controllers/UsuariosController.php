<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class UsuariosController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        return view('usuarios/index');
    }

    public function getUsuarios()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Se excluye la clave en el listado por seguridad
        $usuarios = $this->usuarioModel->select('id_usuario, nombre, correo, rol, estado')->findAll();
        return $this->response->setJSON(['data' => $usuarios]);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id     = $this->request->getPost('id_usuario');
        $nombre = trim((string) $this->request->getPost('nombre'));
        $correo = trim((string) $this->request->getPost('correo'));
        $clave  = (string) $this->request->getPost('clave');
        $rol    = $this->request->getPost('rol');
        $estado = $this->request->getPost('estado');

        // Validación manual de la clave segun si es creación o edición
        if (empty($id) && empty($clave)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['clave' => 'La contraseña es obligatoria para nuevos usuarios.']
            ]);
        }

        if (!empty($clave) && strlen($clave) < 6) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['clave' => 'La contraseña debe tener al menos 6 caracteres.']
            ]);
        }

        $data = [
            'nombre' => $nombre,
            'correo' => $correo,
            'rol'    => $rol,
            'estado' => $estado
        ];

        // Solo adjuntamos la clave si el usuario ingresó una
        if (!empty($clave)) {
            $data['clave'] = $clave;
        }

        if (!empty($id)) {
            // Edición de usuario
            if (!$this->usuarioModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->usuarioModel->errors()
                ]);
            }
            $message = 'Usuario actualizado correctamente.';
        } else {
            // Registro de usuario nuevo
            if (!$this->usuarioModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->usuarioModel->errors()
                ]);
            }
            $message = 'Usuario registrado con éxito.';
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    public function obtener($id)
    {
        $usuario = $this->usuarioModel->select('id_usuario, nombre, correo, rol, estado')->find($id);

        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $usuario]);
    }

    public function eliminar($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Evitar que el usuario se elimine a sí mismo
        if ($id == session('id_usuario')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No puedes eliminar tu propia cuenta de usuario en sesión.'
            ]);
        }

        try {
            if ($this->usuarioModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado con éxito.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No se puede eliminar el usuario porque tiene transacciones asociadas (ventas o compras).'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al intentar eliminar.']);
    }
}