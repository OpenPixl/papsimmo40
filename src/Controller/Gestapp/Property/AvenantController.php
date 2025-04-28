<?php

namespace App\Controller\Gestapp\Property;

use App\Entity\Gestapp\Property\Avenant;
use App\Form\Gestapp\Property\AvenantType;
use App\Repository\Gestapp\Property\AvenantRepository;
use App\Repository\Gestapp\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gestapp/property/avenant')]
final class AvenantController extends AbstractController
{
    #[Route(name: 'app_gestapp_property_avenant_index', methods: ['GET'])]
    public function index(AvenantRepository $avenantRepository): Response
    {
        return $this->render('gestapp/property/avenant/index.html.twig', [
            'avenants' => $avenantRepository->findAll(),
        ]);
    }

    #[Route('/new/{idproperty}', name: 'op_gestapp_property_avenant_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        PropertyRepository $propertyRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        $idproperty
    ): Response
    {
        $property = $propertyRepository->find($idproperty);
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];

        $hasAvenants = $property->getAvenants();
        if(!$hasAvenants){
            $firstAvenant = new Avenant();
            $firstAvenant->setDateAvenant($property->getCreatedAt());
            $firstAvenant->setPrice($property->getPrice());
            $firstAvenant->setHonoraires($property->getHonoraires());
            $firstAvenant->setPriceFai($property->getPriceFai());
            $firstAvenant->setIsFirstAvenant(1);
            $entityManager->persist($firstAvenant);
            $entityManager->flush();
        }

        $avenant = new Avenant();
        $avenant->setDateAvenant(new \DateTime());
        $avenant->setPrice($property->getPrice());
        $avenant->setHonoraires($property->getHonoraires());
        $avenant->setPriceFai($property->getPriceFai());

        $form = $this->createForm(AvenantType::class, $avenant,[
            'action' => $this->generateUrl('op_gestapp_property_avenant_new', ['idproperty' => $idproperty]),
            'method' => 'POST',
            'attr' => [
                'id' => 'formAvenant_add',
            ]
        ]);
        $form->handleRequest($request);

        $date = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $avenant->setIsFirstAvenant(0);
            $avenant->setProperty($property);
            $avenantPdf = $form->get('avenantName')->getData();
            if($avenantPdf){
                $pathdir = $this->getParameter('property_doc_directory')."/".$newref."/documents/";

                if($property->getDupMandat()){
                    $refMandat = $property->getRefMandat().$property->getDupMandat();
                }else{
                    $refMandat = $property->getRefMandat();
                }
                $newFilename = 'av-m'.$refMandat.'-'.$date->format('dmY').'.'.$avenantPdf->guessExtension();
                try {
                    if (is_dir($pathdir)){
                        $avenantPdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }else{
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir."/", 0775, true);
                        // Déplacement de la photo
                        $avenantPdf->move(
                            $this->getParameter('property_doc_directory')."/".$newref."/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $avenant->setPathdir($newref."/documents/");
                $avenant->setAvenantName($newFilename);
            }



            $property->setPrice($form->get('price')->getData());
            $property->setHonoraires($form->get('honoraires')->getData());
            $property->setPriceFai($form->get('priceFai')->getData());

            $entityManager->persist($avenant);
            $entityManager->flush();

            return  $this->json([
                'code'=> 200,
                'message' => "L'avenant a été ajouté avec success",
            ], 200);
        }

        $view = $this->render('gestapp/property/avenant/new.html.twig', [
            'avenant' => $avenant,
            'form' => $form,
        ]);

        return  $this->json([
            'code'=> 200,
            'form' => $view->getContent(),
        ], 200);
    }

    #[Route('/{id}', name: 'app_gestapp_property_avenant_show', methods: ['GET'])]
    public function show(Avenant $avenant): Response
    {
        return $this->render('gestapp/property/avenant/show.html.twig', [
            'avenant' => $avenant,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_gestapp_property_avenant_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Avenant $avenant,
        PropertyRepository $propertyRepository,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
    ): Response
    {
        $form = $this->createForm(AvenantType::class, $avenant);
        $form->handleRequest($request);

        $idproperty = $avenant->getProperty()->getId();
        $property = $propertyRepository->find($idproperty);
        $ref = explode("/", $property->getRef());
        $newref = $ref[0].'-'.$ref[1];
        $date = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $avenantName = $avenant->getAvenantName();

            $avenantPdf = $form->get('avenantName')->getData();
            if($avenantPdf) {

                $pathdir = $this->getParameter('property_doc_directory') . "/" . $newref . "/documents/";
                $pathfile = $pathdir . $avenantName;
                if ($avenantName) {
                    // On vérifie si l'image existe
                    if (file_exists($pathfile)) {
                        unlink($pathfile);
                    }
                }
                if($property->getDupMandat()){
                    $refMandat = $property->getRefMandat().$property->getDupMandat();
                }else{
                    $refMandat = $property->getRefMandat();
                }
                $newFilename = 'av-m'.$refMandat.'-'.$date->format('dmY').'.'.$avenantPdf->guessExtension();
                try {
                    if (is_dir($pathdir)) {
                        $avenantPdf->move(
                            $this->getParameter('property_doc_directory') . "/" . $newref . "/documents/",
                            $newFilename
                        );
                    } else {
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir . "/", 0775, true);
                        // Déplacement de la photo
                        $avenantPdf->move(
                            $this->getParameter('property_doc_directory') . "/" . $newref . "/documents/",
                            $newFilename
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $avenant->setAvenantName($newFilename);
                $em->flush();
            }
        }

        $view = $this->render('gestapp/property/avenant/new.html.twig', [
            'avenant' => $avenant,
            'form' => $form,
        ]);

        return  $this->json([
            'code'=> 200,
            'form' => $view->getContent(),
        ], 200);
    }

    #[Route('/{id}', name: 'app_gestapp_property_avenant_delete', methods: ['POST'])]
    public function delete(Request $request, Avenant $avenant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avenant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($avenant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestapp_property_avenant_index', [], Response::HTTP_SEE_OTHER);
    }
}
