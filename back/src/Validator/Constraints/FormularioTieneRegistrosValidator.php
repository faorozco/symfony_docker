<?php
// api/src/Validator/Constraints/MinimalPropertiesValidator.php

namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
final class FormularioTieneRegistrosValidator extends ConstraintValidator
{

    // /**
    //  * @var EntityManager
    //  */
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }

    public function validate($value, Constraint $constraint): void
    {
        //Verificar si esa tabla de retención documental 
        $registros=$value;
        $cantidadRegistros=count($value);
        if ($cantidadRegistros>0) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ cantidadRegistros }}', $cantidadRegistros)
            ->addViolation();
        }
    }
}
