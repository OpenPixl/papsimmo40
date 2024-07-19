<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestapp/downloaddocument')]
class DownloadDocumentController extends AbstractController
{
    #[Route('/docs', name: 'app_admin_download_document_docs')]
    public function transaction(): Response
    {
        $directory = $this->getParameter('kernel.project_dir') . '/public/properties';
        $contents = $this->scanDirectory($directory);

        //dd($contents);

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
