<?php

namespace App\Reports\Factories;

use App\Reports\Contracts\ReportGeneratorInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;

class ReportGeneratorFactory
{
    public static function make(string $reportType): ReportGeneratorInterface
    {
        // Namespace onde as classes de relatório estão localizadas
        $namespace = 'App\\Reports\\';

        // Monta o nome da classe de relatório
        $className = $namespace.Str::studly($reportType).'Report';

        // Busco pelas classes que implementam a interface ReportGeneratorInterface
        if (class_exists($className) && (new ReflectionClass($className))->implementsInterface(ReportGeneratorInterface::class)) {
            return new $className();
        }

        throw new InvalidArgumentException('Tipo de relatório inválido');
    }
}
