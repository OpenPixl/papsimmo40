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
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');

        return $this->render('gestapp/document/index.html.twig', [
            'documents' => $documentRepository->findAll(),
            'hasAccess' => $hasAccess
        ]);
    }

    #[Route('/{idcat}', name: 'op_gestapp_document_categorie', methods: ['GET'])]
    public function categorie(DocumentRepository $documentRepository, $idcat): Response
    {
        // filtrages des ressources par catégorie
        $documents = $documentRepository->findBy(['category' => $idcat]);

        //dd($documents);

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

        return $this->render('gestapp/document/new.html.twig', [
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

        if ($form->isSubmitted() && $form->isValid()) {

            $fileFileName = $form->get('fileFilename')->getData();
            $category = $form->get('category')->getData();
            if($fileFileName){
                $ext = $fileFileName->guessExtension();

                $originalFileName = pathinfo($fileFileName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '.' . $ext;
                if($ext == 'pdf') {
                    try {
                        $fileFileName->move(
                            $this->getParameter('document_pdf_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $document->setTypeDoc('Pdf');
                    $document->setName($newFileName);
                    $document->setPdf($newFileName);
                }
                elseif($ext == 'docx' || $ext == 'doc' || $ext == 'odt'){
                    try {
                        $fileFileName->move(
                            $this->getParameter('document_word_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $document->setTypeDoc('Word');
                    $document->setName($newFileName);
                    $document->setDoc($newFileName);
                }elseif($ext == 'xls' || $ext == 'xlsx' || $ext == 'ods'){
                    try {
                        $fileFileName->move(
                            $this->getParameter('document_excel_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $document->setTypeDoc('Excel');
                    $document->setName($newFileName);
                    $document->setSheet($newFileName);
                }elseif($ext == 'mp4'){
                    //dd($ext);
                    try {
                        $fileFileName->move(
                            $this->getParameter('document_mp4_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $document->setTypeDoc('Mp4');
                    $document->setName($newFileName);
                    $document->setMp4($newFileName);
                }
            }

            if($lastdocument == null){
                $document->setPosition(1);
            }else{
                $document->setPosition($lastdocument->getPosition() + 1);
            }
            $documentRepository->add($document, true);

            $documents = $documentRepository->findBy(['category' => $category]);

            return $this->json([
                'code' => 200,
                'message' => "Document ajouté à la BDD.",
                'liste' => $this->renderView('gestapp/document/include/_liste.html.twig',[
                    'documents' => $documents
                ])

            ], 200);
        }
        //dd($form->isValid());
        return $this->render('gestapp/document/new2.html.twig', [
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

        return $this->render('gestapp/document/edit.html.twig', [
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
        $category = $document->getCategory();
        if($name)
        {
            if($typeDoc =='Pdf')
            {
                $directory = 'document_pdf_directory';
            }
            elseif($typeDoc =='Word')
            {
                $directory = 'document_word_directory';
            }
            elseif($typeDoc == 'Excel')
            {
                $directory = 'document_excel_directory';
            }
            elseif($typeDoc == 'Mp4')
            {
                $directory = 'document_mp4_directory';
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
        $documents = $documentRepository->findBy(['category' => $category]);

        return $this->json([
            'code' => '200',
            'message' => 'Le document a été correctement supprimé.',
            'liste' => $this->renderView('gestapp/document/include/_liste.html.twig',[
                'documents' => $documents
            ])
        ], 200);
    }


}
