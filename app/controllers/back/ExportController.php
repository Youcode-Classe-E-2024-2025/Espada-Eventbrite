<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\ExportService;

class ExportController extends Controller
{
    private ExportService $exportService;

    public function __construct()
    {
        parent::__construct();
        $this->exportService = new ExportService();
    }

    public function exportCsv()
    {
        $data = $this->exportService->getCsvData();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="exported_data.csv"');
        echo $data;
    }

    public function exportPdf()
    {
        $data = $this->exportService->getPdfData();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="exported_data.pdf"');
        echo $data;
    }
}
