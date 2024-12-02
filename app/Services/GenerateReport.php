<?php
namespace App\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class GenerateReport{
    public function generateReport($reportType, $allData,$applyFilter, $params=null) {
        // print_r($params);
        // exit();
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle($reportType);
        
        $headers = array_keys(is_array($allData[0]) ? $allData[0] : (array)$allData[0]);
        $columnCount = count($headers);
        
        // Styling constants
        $titleColor = '1F4E78';
        $subtitleColor = '7F7F7F';
        $headerColor = '305496';
        $oddRowColor = 'F2F2F2';
        $evenRowColor = 'FFFFFF';
        $highPriorityColor = 'FFC7CE';
        
        // Title
        $worksheet->mergeCells('A1:' . chr(64 + $columnCount) . '1');
        $worksheet->setCellValue('A1', strtoupper($reportType));
        $worksheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => $titleColor]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $worksheet->getRowDimension(1)->setRowHeight(30);
        
        // Subtitle
        $worksheet->mergeCells('A2:' . chr(64 + $columnCount) . '2');
        $worksheet->setCellValue('A2', "Generated on " . date('F j, Y'));
        $worksheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 12, 'color' => ['rgb' => $subtitleColor]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        
        $rowIndex = 4;
        
        // Headers
        $headerRange = "A$rowIndex:" . chr(64 + $columnCount) . $rowIndex;
        $worksheet->fromArray($headers, null, "A$rowIndex");
        $worksheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $headerColor]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
        ]);
        $worksheet->getRowDimension($rowIndex)->setRowHeight(30);
        $rowIndex++;
        
        // Data rows
        $maxLengths = array_fill(0, $columnCount, 0);
        foreach ($allData as $index => $item) {
            $rowData = [];
            $itemArray = (array)$item;
            foreach ($headers as $colIndex => $header) {
                $value = $itemArray[$header] ?? '';
                $value = is_array($value) ? implode(", ", $value) : $value;
                $rowData[] = $value;
                $maxLengths[$colIndex] = max($maxLengths[$colIndex], strlen($value));
            }
            
            $currentRange = "A$rowIndex:" . chr(64 + $columnCount) . $rowIndex;
            $worksheet->fromArray($rowData, null, "A$rowIndex");
            
            $fillColor = $index % 2 === 0 ? $evenRowColor : $oddRowColor;
            $worksheet->getStyle($currentRange)->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $fillColor]],
                'alignment' => ['vertical' => 'top', 'wrapText' => true],
            ]);
            
            if ($reportType === 'BacklogReport' && ($itemArray['Priority'] ?? '') === 'H') {
                $worksheet->getStyle($currentRange)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'FF0000']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $highPriorityColor]],
                ]);
            }
            
            $worksheet->getRowDimension($rowIndex)->setRowHeight(-1);  // Auto-height
            $rowIndex++;
        }
        
        // Set column widths based on content
        foreach (range(0, $columnCount - 1) as $colIndex) {
            $column = chr(65 + $colIndex);
            $width = min(max($maxLengths[$colIndex], strlen($headers[$colIndex])) + 2, 50);  // +2 for padding, max 50
            $worksheet->getColumnDimension($column)->setWidth($width);
        }
        
        // Apply borders
        $worksheet->getStyle('A1:' . chr(64 + $columnCount) . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        
        // Create and output the file
        $writer = new Xlsx($spreadsheet);
        $fileName = "{$reportType}_" . date('Y-m-d') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}