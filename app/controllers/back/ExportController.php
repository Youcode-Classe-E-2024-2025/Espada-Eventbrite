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
            $this->logger->info('Exporting data to csv');

            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->security->validateCsrfToken($csrfToken)) {
                $this->logger->error('Invalid CSRF token.');
                $this->session->set('error', 'Invalid CSRF token.');
                throw new \Exception('Invalid CSRF token.');
            }

            $data = $this->exportService->getCsvData();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $data;

            $this->session->set('success', 'Data exported successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error exporting data to CSV: ' . $e->getMessage());
            $this->session->set('error', 'Error exporting data to CSV: ' . $e->getMessage());
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $this->logger->info('Exporting data to PDF');

            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->security->validateCsrfToken($csrfToken)) {
                $this->logger->error('Invalid CSRF token.');
                $this->session->set('error', 'Invalid CSRF token.');
                $this->redirect('/admin/export');
                throw new \Exception('Invalid CSRF token.');
            }

            $data = $this->exportService->getPdfData();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="export.pdf"');
            echo $data;

            $this->session->set('success', 'Data exported to PDF successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error exporting data to PDF: ' . $e->getMessage());
            $this->session->set('error', 'Error exporting data to PDF.');
            $this->redirect('/admin/export');
            exit;
        }
    }
}
