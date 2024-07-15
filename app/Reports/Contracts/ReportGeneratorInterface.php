<?php

namespace App\Reports\Contracts;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ReportGeneratorInterface
{
    public function generate(): BinaryFileResponse;
}
