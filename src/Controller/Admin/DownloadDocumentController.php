<?php

namespace App\Controller\Admin;

use App\Repository\Gestapp\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/downloaddocument')]
class DownloadDocumentController extends AbstractController
{
    #[Route('/docs', name: 'app_admin_download_document_docs')]
    public function transaction(PropertyRepository $propertyRepository): Response
    {
        $directory = $this->getParameter('kernel.project_dir') . '/public/properties';
        $scans = $this->scanDirectory($directory);
        //dd($scans);
        $properties = $propertyRepository->findAll();
        $contents = [];

        foreach ($scans as $scan) {
            $refscan = explode('-', $scan['name']);
            $ref = $refscan[0].'/'.$refscan[1].'-'.$refscan[2];
            foreach ($properties as $property) {
                if($property->getRef() === $ref) {
                    $dupMandat = $property->getDupMandat();
                    if(!$dupMandat){
                        $mandat = $property->getRefMandat();
                    }else{
                        $mandat = $property->getRefMandat().$dupMandat;
                    }
                    $row = array(
                        'id' => $property->getRefMandat(),
                        'type' => $scan['type'],
                        'name' => $scan['name'],
                        'path' => $scan['path'],
                        'children' => $scan['children'],
                        'mandat' => $mandat
                    );
                    array_push($contents, $row);
                }
            }
        }

        usort($contents, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        return $this->render('admin/download_document/transaction.html.twig', [
            'contents' => $contents,
        ]);
    }

    #[Route('/customers', name: 'app_admin_download_document_client')]
    public function customer(): Response
    {
        $directory = $this->getParameter('kernel.project_dir') . '/public/customer';
        $contents = $this->scanDirectory($directory);

        //dd($contents);

        return $this->render('admin/download_document/customer.html.twig', [
            'contents' => $contents,
        ]);
    }

    private function scanDirectory(string $directory): array
    {
        $result = [];
        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $directory . '/' . $file;

            if (is_dir($path)) {
                $result[] = [
                    'type' => 'directory',
                    'name' => $file,
                    'path' => str_replace($this->getParameter('kernel.project_dir') . '/public', '', $path),
                    'children' => $this->scanDirectory($path),
                ];
            } else {
                $result[] = [
                    'type' => 'file',
                    'name' => $file,
                    'path' => str_replace($this->getParameter('kernel.project_dir') . '/public', '', $path),
                ];
            }
        }

        return $result;
    }
}
