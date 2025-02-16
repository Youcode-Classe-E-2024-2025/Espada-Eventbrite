<?php

namespace App\controllers\front;

use App\core\Controller;
use App\services\ExportService;

class OrganizerExportController extends Controller
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
            $organizerId = $this->session->get('user')->id;
            $data = $this->exportService->getOrganizerCsvData($organizerId);

            // Clear any output buffers
            ob_clean();

            // Set headers for file download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Output CSV data
            echo $data;
            exit();
        } catch (\Exception $e) {
            $this->logger->error('Export error: ' . $e->getMessage());
            exit('Export failed: ' . $e->getMessage());
        }
    }

    public function exportPdf($organizerId)
    {
        ob_clean();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="events_report.pdf"');
        echo $this->exportService->getOrganizerPdfData($organizerId);
        exit();
    }
}
