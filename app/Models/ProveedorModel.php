<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{
    protected $table            = 'proveedor';
    protected $primaryKey       = 'id_proveedor';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['identificacion', 'nombre', 'telefono'];

    // Reglas centralizadas en el Modelo
    protected $validationRules = [
        'identificacion' => 'required|numeric|min_length[10]|max_length[13]|is_unique[proveedor.identificacion,id_proveedor,{id_proveedor}]',
        'nombre'         => 'required|min_length[3]|max_length[100]',
        'telefono'       => 'permit_empty|min_length[7]|max_length[20]',
    ];

    protected $validationMessages = [
        'identificacion' => [
            'required'   => 'La identificación (Cédula o RUC) es obligatoria.',
            'numeric'    => 'La identificación debe contener solo números.',
            'min_length' => 'La identificación debe tener al menos 10 dígitos.',
            'max_length' => 'La identificación no puede exceder los 13 dígitos.',
            'is_unique'  => 'Esta identificación ya se encuentra registrada.',
        ],
        'nombre' => [
            'required'   => 'El nombre o razón social del proveedor es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder los 100 caracteres.',
        ],
        'telefono' => [
            'min_length' => 'El teléfono debe tener al menos 7 caracteres.',
            'max_length' => 'El teléfono no puede exceder los 20 caracteres.',
        ],
    ];

    /**
     * Valida Identificación Ecuatoriana (Cédula 10 dígitos o RUC Persona Natural 13 dígitos)
     */
    public function esIdentificacionValida(string $identificacion): bool
    {
        $longitud = strlen($identificacion);

        if (!ctype_digit($identificacion)) {
            return false;
        }

        if ($longitud === 10) {
            return $this->validarCedula($identificacion);
        }

        if ($longitud === 13) {
            // RUC Persona Natural: Los primeros 10 dígitos son la cédula y termina en 001
            $cedulaBase = substr($identificacion, 0, 10);
            $establecimiento = substr($identificacion, 10, 3);

            return $this->validarCedula($cedulaBase) && $establecimiento === '001';
        }

        return false;
    }

    private function validarCedula(string $cedula): bool
    {
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