<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FormularioTieneRegistros extends Constraint
{
    public $message = 'El formulario no puede cambiarse tiene "{{ cantidadRegistros }}" registros relacionados';
}
