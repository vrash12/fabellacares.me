<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ServedTokensExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function collection(): Collection
    {
        return DB::table('tokens')
            ->join('queues', 'tokens.queue_id', '=', 'queues.id')
            ->whereNotNull('tokens.served_at')
            ->whereBetween('tokens.served_at', [$this->from, $this->to])
            ->orderBy('queues.name')
            ->orderBy('tokens.served_at')
            ->get([
                'queues.name   as Department',
                'tokens.code   as Token',
                DB::raw('DATE_FORMAT(tokens.served_at,"%Y-%m-%d %H:%i") as Served_At')
            ]);
    }

    public function headings(): array
    {
        return ['Department', 'Token', 'Served At'];
    }
}
