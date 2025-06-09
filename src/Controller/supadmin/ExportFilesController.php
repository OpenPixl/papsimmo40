<?php

namespace App\Controller\supadmin;

use App\Repository\Gestapp\PropertyRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportFilesController extends AbstractController
{
    #[Route('/supadmin/export/files', name: 'app_supadmin_export_excel')]
    public function exportexcel(PropertyRepository $propertyRepository): Response
    {
        // 1. Récupérer les données
        $biens = $propertyRepository->listPublication(); // à créer

        //dd($biens);

        // 2. Créer le fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. En-têtes
        $sheet->fromArray(['ID', 'Nom', 'Adresse', 'Prix'], null, 'A1');

        // 4. Données
        $row = 2;
        foreach ($biens as $bien) {
            $sheet->setCellValue("A$row", $bien['id']);
            $sheet->setCellValue("B$row", $bien['name']);
            $sheet->setCellValue("C$row", $bien['city']);
            $sheet->setCellValue("D$row", $bien['priceFai']);
            $row++;
        }

        // 5. Envoyer le fichier en réponse
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $dispositionHeader = $response->headers->makeDisposition(
            'attachment',
            'biens_diffuses.xlsx'
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

#[Route('/supadmin/import/files', name: 'app_supadmin_import_excel')]
    public function importexcel(): Response
    {
        return $this->render('supadmin/import_files/import.html.twig', [
            'controller_name' => 'ImportFilesController',
        ]);
    }
}
