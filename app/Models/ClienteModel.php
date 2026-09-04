<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'cliente';
    protected $primaryKey       = 'id_cliente';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['identificacion', 'nombre', 'telefono', 'correo'];

    // Usamos las reglas nativas de CodeIgniter 4
    protected $validationRules = [
        'identificacion' => 'required|numeric|exact_length[10]|is_unique[cliente.identificacion,id_cliente,{id_cliente}]',
        'nombre'         => 'required|min_length[3]|max_length[100]',
        'telefono'       => 'permit_empty|min_length[7]|max_length[20]',
        'correo'         => 'permit_empty|valid_email|max_length[100]',
    ];

    protected $validationMessages = [
        'identificacion' => [
            'required'     => 'La cédula de identidad es obligatoria.',
            'numeric'      => 'La cédula debe contener solo números.',
            'exact_length' => 'La cédula debe tener exactamente 10 dígitos.',
            'is_unique'    => 'Esta cédula ya se encuentra registrada.',
        ],
        'nombre' => [
            'required'   => 'El nombre del cliente es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.',
        ],
        'telefono' => [
            'min_length' => 'El teléfono debe tener al menos 7 caracteres.',
            'max_length' => 'El teléfono no puede exceder los 20 caracteres.',
        ],
        'correo' => [
            'valid_email' => 'Ingrese una dirección de correo electrónico válida.',
            'max_length'  => 'El correo no puede exceder los 100 caracteres.',
        ],
    ];

    /**
     * Algoritmo Módulo 10 para Validar Cédula Ecuatoriana
     */
    public function esCedulaValida(string $cedula): bool
    {
        if (strlen($cedula) !== 10 || !ctype_digit($cedula)) {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || ($provincia > 24 && $provincia !== 30)) {
            return false;
        }

        $tercerDigito = (int) $cedula[2];
        if ($tercerDigito >= 6) {
            return false;
        }

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $digitoVerificador = (int) $cedula[9];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $valor = (int) $cedula[$i] * $coeficientes[$i];
            $suma += ($valor >= 10) ? $valor - 9 : $valor;
        }

        $digitoObtenido = ($suma % 10 === 0) ? 0 : 10 - ($suma % 10);

        return $digitoObtenido === $digitoVerificador;
    }
}