<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\ESGReport;

class EsgReportExport implements WithHeadings, FromQuery, WithMapping
{
    use Exportable;

    private $i = 1;

    /*prepare the Query for export with Filter  */
    public function query()
    {
        return ESGReport::query()->select('id', 'email')->orderBy('id', 'DESC');
    }


    /* for Handing In Excel File*/
    public function headings(): array
    {
        return [
            'Sr. No.',
            'Email'
        ];
    }

    /* Mapping for result */
    public function map($inputMap): array
    {
        return [
            $this->i++,
            !empty($inputMap->email) ? $inputMap->email : "No email found",
        ];
    }
}
