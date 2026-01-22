<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $reports;
    protected $rowNumber = 0;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return collect($this->reports);
    }

    public function headings(): array
    {
        return [
            'S/N',
            'APPLICANT NAME',
            'EMAIL',
            'MOBILE',
            'COURSE',
            'BRANCH',
            'VENUE',
            'TOTAL FEE (TZS)',
            'PAID AMOUNT (TZS)',
            'BALANCE (TZS)',
            'PAYMENT %',
            'STATUS',
            'INVOICE NUMBER',
            'RECEIPT NUMBERS',
            'DISCOUNT (TZS)',
            'INSTALLMENT TYPE',
            'INVOICE DATE',
            'START DATE',
            'END DATE',
            'EDUCATION LEVEL',
            'DURATION'
        ];
    }

    public function map($report): array
    {
        $this->rowNumber++;
        
        // Handle both old and new data structures
        return [
            $this->rowNumber,
            $report['full_name'] ?? $report['name'] ?? '-',
            $report['email'] ?? '-',
            $report['mobile'] ?? '-',
            $report['course_name'] ?? $report['course_enrolled'] ?? '-',
            $report['branch_name'] ?? (is_array($report['branch'] ?? null) ? ($report['branch']['name'] ?? '-') : '-'),
            $report['venue_name'] ?? $report['venue'] ?? '-',
            $report['total_fee'] ?? $report['payment'] ?? 0,
            $report['paid_amount'] ?? $report['paid'] ?? 0,
            $report['balance'] ?? 0,
            ($report['payment_percentage'] ?? 0) . '%',
            $report['invoice_status'] ?? 'Unpaid',
            $report['invoice_number'] ?? $report['invoice_no'] ?? $report['receipt_no'] ?? '-',
            $report['receipt_numbers'] ?? $report['remark'] ?? '-',
            $report['discount_amount'] ?? 0,
            $report['installment_type'] ?? '-',
            $report['invoice_date'] ?? $report['date_issued'] ?? '-',
            $report['start_date'] ?? '-',
            $report['end_date'] ?? '-',
            $report['education_level'] ?? '-',
            $report['duration'] ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row (will be row 5 after insertions)
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667EEA']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Get first record for header info
                $first = $this->reports[0] ?? [];
                $course = strtoupper($first['course_name'] ?? $first['course_enrolled'] ?? 'ALL COURSES');
                $venue = strtoupper($first['venue_name'] ?? $first['venue'] ?? 'ALL VENUES');
                $branch = strtoupper($first['branch_name'] ?? (is_array($first['branch'] ?? null) ? ($first['branch']['name'] ?? 'ALL BRANCHES') : 'ALL BRANCHES'));
                $start = $first['start_date'] ?? '-';
                $end = $first['end_date'] ?? '-';

                // Calculate summary statistics
                $totalRecords = count($this->reports);
                $totalRevenue = array_sum(array_column($this->reports, 'paid_amount')) ?: array_sum(array_column($this->reports, 'paid'));
                $totalBalance = array_sum(array_column($this->reports, 'balance'));
                $totalExpected = $totalRevenue + $totalBalance;

                // Insert header rows
                $headerLines = 8; // Title + info + summary stats
                $sheet->insertNewRowBefore(1, $headerLines);

                // Row 1: Title
                $sheet->setCellValue('A1', 'APPLICANTS REPORT');
                $sheet->mergeCells('A1:U1');
                $sheet->getStyle('A1:U1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '667EEA']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Row 2: Generated Date
                $sheet->setCellValue('A2', 'Generated: ' . now()->format('d/m/Y H:i:s'));
                $sheet->mergeCells('A2:U2');
                $sheet->getStyle('A2:U2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Row 3: Course Info
                $sheet->setCellValue('A3', 'COURSE: ' . $course);
                $sheet->mergeCells('A3:U3');
                $sheet->getStyle('A3:U3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Row 4: Branch & Venue
                $sheet->setCellValue('A4', 'BRANCH: ' . $branch . ' | VENUE: ' . $venue);
                $sheet->mergeCells('A4:U4');
                $sheet->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Row 5: Dates
                $sheet->setCellValue('A5', 'PERIOD: START: ' . $start . ' | END: ' . $end);
                $sheet->mergeCells('A5:U5');
                $sheet->getStyle('A5:U5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Row 6: Empty
                
                // Row 7: Summary Statistics Header
                $sheet->setCellValue('A7', 'SUMMARY STATISTICS');
                $sheet->mergeCells('A7:U7');
                $sheet->getStyle('A7:U7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Row 8: Summary Data
                $summaryText = sprintf(
                    'Total Records: %d | Total Revenue: TZS %s | Pending Balance: TZS %s | Collection Rate: %.1f%%',
                    $totalRecords,
                    number_format($totalRevenue, 2),
                    number_format($totalBalance, 2),
                    $totalExpected > 0 ? ($totalRevenue / $totalExpected) * 100 : 0
                );
                $sheet->setCellValue('A8', $summaryText);
                $sheet->mergeCells('A8:U8');
                $sheet->getStyle('A8:U8')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Style the heading row (now at row 9)
                $headingRow = $headerLines + 1;
                $sheet->getStyle('A'.$headingRow.':U'.$headingRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '667EEA']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Wrap text for all columns
                $sheet->getStyle('A:U')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A:U')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Auto-size columns
                foreach (range('A', 'U') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Freeze pane below heading row
                $sheet->freezePane('A'.($headingRow+1));
                
                // Autofilter on header
                $highestColumn = $sheet->getHighestColumn();
                $sheet->setAutoFilter('A'.$headingRow.':'.$highestColumn.$headingRow);

                // Add alternating row colors for data
                $lastDataRow = $sheet->getHighestRow();
                for ($row = $headingRow + 1; $row <= $lastDataRow; $row++) {
                    if (($row - $headingRow) % 2 == 0) {
                        $sheet->getStyle('A'.$row.':U'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8F9FA']
                            ]
                        ]);
                    }
                }

                // Add signature section
                $sigRow1 = $lastDataRow + 3;
                $sigRow2 = $lastDataRow + 5;
                
                $sheet->setCellValue('A'.$sigRow1, 'Instructor Name: ......................................  Signature: ...............................  Date: ..........................');
                $sheet->mergeCells('A'.$sigRow1.':U'.$sigRow1);
                $sheet->getStyle('A'.$sigRow1.':U'.$sigRow1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A'.$sigRow2, 'Coordinator ICT Training and Marketing: ......................................  Signature: ................................  Date: .................................');
                $sheet->mergeCells('A'.$sigRow2.':U'.$sigRow2);
                $sheet->getStyle('A'.$sigRow2.':U'.$sigRow2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
