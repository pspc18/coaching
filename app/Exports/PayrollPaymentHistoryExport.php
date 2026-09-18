<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollPaymentHistoryExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    private $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            '#', 'Staff ID', 'Staff Name', 'Payroll Period', 'Payment Date',
            'Amount', 'Recorded By', 'Net Payable', 'Total Paid', 'Pending',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:J1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF0F3D56');
        $sheet->getStyle('F:J')->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [];
    }
}
