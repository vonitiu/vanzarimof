<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExporter
{
    private mysqli $db;

    public function __construct()
    {
        $this->db =
            Database::getConnection();
    }

    public function exportOffers(
        array $filters=[]
    ): string
    {
        $spreadsheet =
            new Spreadsheet();

        $sheet =
            $spreadsheet
            ->getActiveSheet();

        $sheet->setCellValue(
            'A1',
            'Offer Number'
        );

        $sheet->setCellValue(
            'B1',
            'Company'
        );

        $sheet->setCellValue(
            'C1',
            'Date'
        );

        $sheet->setCellValue(
            'D1',
            'Responsible'
        );

        $sheet->setCellValue(
            'E1',
            'Department'
        );

        $sheet->setCellValue(
            'F1',
            'Status'
        );

        $sheet->setCellValue(
            'G1',
            'Total'
        );

        $result =
            $this->db->query(
            "
            SELECT *
            FROM vw_offer_summary
            ORDER BY id DESC
            "
        );

        $rowNo = 2;

        while(
            $row =
            $result->fetch_assoc()
        )
        {
            $sheet->setCellValue(
                'A'.$rowNo,
                $row['numaroferta']
            );

            $sheet->setCellValue(
                'B'.$rowNo,
                $row['firma']
            );

            $sheet->setCellValue(
                'C'.$rowNo,
                $row['data']
            );

            $sheet->setCellValue(
                'D'.$rowNo,
                $row['responsabil']
            );

            $sheet->setCellValue(
                'E'.$rowNo,
                $row['departament']
            );

            $sheet->setCellValue(
                'F'.$rowNo,
                $row['stareoferta']
            );

            $sheet->setCellValue(
                'G'.$rowNo,
                $row['offer_total']
            );

            $rowNo++;
        }

        $filename =
            'offers_' .
            date('Ymd_His') .
            '.xlsx';

        $fullPath =
            __DIR__ .
            '/../storage/exports/' .
            $filename;

        $writer =
            new Xlsx(
                $spreadsheet
            );

        $writer->save(
            $fullPath
        );

        return $filename;
    }
}