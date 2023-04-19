<?php

namespace App\Filter;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

final class ORSearchFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'query') {
            return false;
        }
        $value = explode("|", $value);

        $reader = new AnnotationReader();
        $reflectionClass = new ReflectionClass(new $resourceClass);
        //$annotation = $reader->getClassAnnotations($reflectionClass);
        $annotation = $reader->getClassAnnotation($reflectionClass, ApiFilter::class);

        if (!$annotation) {
            throw new NotAcceptableHttpException('No Search implemented.');
        }
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $search = [];
        $mappedJoins = [];
        //verificar su puedo organizar el SQL con el $value capturado
        foreach ($annotation->properties as $field) {
            $joins = explode(".", $field);
            for ($lastAlias = 'o', $i = 0, $num = count($joins); $i < $num; $i++) {
                $currentAlias = $joins[$i];
                if ($i === $num - 1) {
                    $search[] = "LOWER({$lastAlias}.{$currentAlias}) LIKE LOWER(:{$parameterName})";
                } else {
                    $join = "{$lastAlias}.{$currentAlias}";
                    if (false === array_search($join, $mappedJoins)) {
                        $queryBuilder->leftJoin($join, $currentAlias);
                        $mappedJoins[] = $join;
                    }
                }

                $lastAlias = $currentAlias;
            }
        }
        $queryBuilder->andWhere(implode(' OR ', $search));
        $queryBuilder->andWhere("o.estado_id = :estado_id");
        $queryBuilder->setParameter($parameterName, '%' . $value[0] . '%');
        if (isset($value[1]) && $value[1] == "isInactiveOnly") {
            $queryBuilder->setParameter("estado_id", "0");
        } else if (!isset($value[1])) {
            $queryBuilder->setParameter("estado_id", "1");
        }

    }

/**
 * @param string $resourceClass
 * @return array
 */
    public function getDescription(string $resourceClass): array
    {
        $reader = new AnnotationReader();
        $reflectionClass = new ReflectionClass(new $resourceClass);
        $annotation = $reader->getClassAnnotation($reflectionClass, ApiFilter::class);
        $description['query'] = [
            'property' => 'search',
            'type' => 'string',
            'required' => false,
            'swagger' => [
                'description' => 'Filter on ' . implode(', ', $annotation->properties),
            ],
        ];

        return $description;
    }
}
