<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuario';
    protected $primaryKey       = 'id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre', 'correo', 'clave', 'rol', 'estado'];

    // Callback para encriptar la clave automáticamente antes de guardar o actualizar
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]',
        'correo' => 'required|valid_email|max_length[100]|is_unique[usuario.correo,id_usuario,{id_usuario}]',
        'rol'    => 'required|in_list[administrador,encargado]',
        'estado' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre del usuario es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.',
        ],
        'correo' => [
            'required'    => 'El correo electrónico es obligatorio.',
            'valid_email' => 'Ingrese una dirección de correo válida.',
            'max_length'  => 'El correo no puede exceder los 100 caracteres.',
            'is_unique'   => 'Este correo electrónico ya está registrado por otro usuario.',
        ],
        'rol' => [
            'required' => 'Debe seleccionar un rol válido.',
            'in_list'  => 'El rol seleccionado no es válido.',
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list'  => 'Estado no válido.',
        ],
    ];

    /**
     * Encripta la contraseña usando password_hash antes de insertar o actualizar
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['clave']) && !empty($data['data']['clave'])) {
            $data['data']['clave'] = password_hash($data['data']['clave'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['clave']);
        }
        return $data;
    }
}