<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(public Collection $records)
    {
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->records;
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
            $row?->class?->name,
            $row?->section?->name,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'E-mail',
            'Class',
            'Section',
        ];
    }
}
