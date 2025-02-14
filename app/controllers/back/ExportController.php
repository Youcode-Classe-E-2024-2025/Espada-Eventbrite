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
        try {
            $data = $this->exportService->getCsvData();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $data;
        } catch (\Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $data = $this->exportService->getPdfData();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="export.pdf"');
            echo $data;
        } catch (\Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}
