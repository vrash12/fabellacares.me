<?php
// app/Exports/TrendExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class TrendExport implements FromArray
{
    /**
     * @var array
     */
    private array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }
}
