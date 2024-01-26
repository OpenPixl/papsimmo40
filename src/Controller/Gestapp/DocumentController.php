<?php

namespace App\Controller\Gestapp;

use App\Entity\Gestapp\choice\CatDocument;
use App\Entity\Gestapp\Document;
use App\Form\Gestapp\DocumentType;
use App\Repository\Gestapp\choice\CatDocumentRepository;
use App\Repository\Gestapp\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gestapp/document')]
class DocumentController extends AbstractController
{
    #[Route('/', name: 'op_gestapp_document_index', methods: ['GET'])]
    public function index(DocumentRepository $documentRepository): Response
    {
        return $this->render('gestapp/document/index.html.twig', [
            'documents' => $documentRepository->findAll(),
        ]);
    }

    #[Route('/{idcat}', name: 'op_gestapp_document_categorie', methods: ['GET'])]
    public function categorie(DocumentRepository $documentRepository, $idcat): Response
    {
        // filtrages des ressources par catégorie
        $documents = $documentRepository->findBy(['category' => $idcat]);

        return $this->json([
            'code' => 200,
            'message' => 'Ok',
            'liste' => $this->renderView('gestapp/document/include/_liste.html.twig', [
                'documents' => $documents
            ])
        ], 200);
    }

    #[Route('/updateposition', name: 'app_gestapp_document_updateposition', methods: ['POST'])]
    public function updatePosition(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $d){
            //dd($d['idcol']);
            // récupérer le doc correspondant à la position
            $doc = $entityManager->getRepository(Document::class)->findOneBy(['position' => $d['idcol']]);
            // mettre à jour le positionnnement
            $doc->setPosition($d['key'] +1);
            // mettre à jour la bdd
            $entityManager->persist($doc);
        }
        $entityManager->flush();

        $documents = $entityManager->getRepository(Document::class)->findAll();

        return $this->json([
            'code' => '200',
            'message' => 'Déplacement effectué',
            'listDocument' => $this->renderView('gestapp/document/include/_liste.html.twig',[
                'documents' => $documents
            ]),
        ], 200);
    }

    #[Route('/new', name: 'op_gestapp_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DocumentRepository $documentRepository): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentRepository->add($document, true);

            return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/document/new.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'op_gestapp_document_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, DocumentRepository $documentRepository, SluggerInterface $slugger): Response
    {
        $document = new Document();
        $lastdocument = $documentRepository->findOneBy([],['position' => 'DESC']);

        $form = $this->createForm(DocumentType::class, $document, [
            'action' => $this->generateUrl('op_gestapp_document_new2'),
            'method' => 'POST'
        ]);
        $form->handleRequest($request);
        //dd($lastdocument);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si Pdf -> code d'injection d'un fichier PDF
            /** @var UploadedFile $logoFile */
            $pdfFileName = $form->get('pdfFilename')->getData();
            if ($pdfFileName) {
                $originalpdfFileName = pathinfo($pdfFileName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safepdfFileName = $slugger->slug($originalpdfFileName);
                $newpdfFileName = $safepdfFileName . '.' . $pdfFileName->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pdfFileName->move(
                        $this->getParameter('pdf_directory'),
                        $newpdfFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $document->setName($newpdfFileName);
                $document->setPdf($newpdfFileName);
            }

            // si Word
            /** @var UploadedFile $logoFile */
            $wordFileName = $form->get('wordFilename')->getData();
            if ($wordFileName) {
                $originalwordFileName = pathinfo($wordFileName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safewordFileName = $slugger->slug($originalwordFileName);
                $newwordFileName = $safewordFileName .'.' . $wordFileName->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $wordFileName->move(
                        $this->getParameter('word_directory'),
                        $newwordFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $document->setName($newwordFileName);
                $document->setDoc($newwordFileName);
            }

            // si Excel
            /** @var UploadedFile $logoFile */
            $excelFileName = $form->get('excelFilename')->getData();
            if ($excelFileName) {
                $originalexcelFileName = pathinfo($excelFileName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeexcelFileName = $slugger->slug($originalexcelFileName);
                $newexcelFileName = $safeexcelFileName . '.' . $excelFileName->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $excelFileName->move(
                        $this->getParameter('excel_directory'),
                        $newexcelFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $document->setName($newexcelFileName);
                $document->setSheet($newexcelFileName);
            }

            // Si Mp4
            /** @var UploadedFile $logoFile */
            $mp4FileName = $form->get('mp4Filename')->getData();
            if ($mp4FileName) {
                $originalmp4FileName = pathinfo($mp4FileName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safemp4FileName = $slugger->slug($originalmp4FileName);
                $newmp4FileName = $safemp4FileName . '.' . $mp4FileName->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $mp4FileName->move(
                        $this->getParameter('mp4_directory'),
                        $newmp4FileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $document->setName($newmp4FileName);
                $document->setMp4($newmp4FileName);
            }
            if($lastdocument == null){
                $document->setPosition(1);
            }else{
                $document->setPosition($lastdocument->getPosition() + 1);
            }
            $documentRepository->add($document, true);

            $documents = $documentRepository->findAll();

            return $this->json([
                'code' => 200,
                'message' => "Document ajouté à la BDD.",
                'list' => $this->renderView('gestapp/document/_ownliste.html.twig',[
                    'documents' => $documents
                ])

            ], 200);
        }

        return $this->renderForm('gestapp/document/new2.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render('gestapp/document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentRepository->add($document, true);

            return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_gestapp_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $documentRepository->remove($document, true);
        }

        return $this->redirectToRoute('op_gestapp_document_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}', name: 'op_gestapp_document_del', methods: ['POST'])]
    public function del(Document $document, DocumentRepository $documentRepository)
    {
        //dd($document);
        $name = $document->getName();
        $typeDoc = $document->getTypeDoc();
        if($name)
        {
            if($typeDoc =='Pdf')
            {
                $directory = 'pdf_directory';
            }
            elseif($typeDoc =='Word')
            {
                $directory = 'word_directory';
            }
            elseif($typeDoc == 'Excel')
            {
                $directory = 'excel_directory';
            }
            elseif($typeDoc == 'Mp4')
            {
                $directory = 'mp4_directory';
            }
            //dd($directory);
            $pathheader = $this->getParameter($directory).'/'.$name;
            //dd($pathheader);
            // On vérifie si l'image existe
            if(file_exists($pathheader)){
                unlink($pathheader);
            }
        }
        $documentRepository->remove($document, true);
        $documents = $documentRepository->findAll();

        return $this->json([
            'code' => '200',
            'message' => 'Le document a été correctement supprimé.',
            'liste' => $this->renderView('gestapp/document/_ownliste.html.twig',[
                'documents' => $documents
            ])
        ], 200);
    }


}
