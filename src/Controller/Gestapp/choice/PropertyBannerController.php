<?php

namespace App\Controller\Gestapp\choice;

use App\Entity\Gestapp\choice\PropertyBanner;
use App\Form\Gestapp\choice\PropertyBannerType;
use App\Repository\Gestapp\choice\PropertyBannerRepository;
use App\Repository\Gestapp\ComplementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/gestapp/choice/property/banner')]
class PropertyBannerController extends AbstractController
{
    #[Route('/', name: 'app_gestapp_choice_property_banner_index', methods: ['GET'])]
    public function index(PropertyBannerRepository $propertyBannerRepository): Response
    {
        return $this->render('gestapp/choice/property_banner/index.html.twig', [
            'property_banners' => $propertyBannerRepository->findAll(),
        ]);
    }

    #[Route('/list', name: 'app_gestapp_choice_property_banner_list', methods: ['GET'])]
    public function list(PropertyBannerRepository $propertyBannerRepository): Response
    {
        return $this->render('gestapp/choice/property_banner/indexjson.html.twig', [
            'property_banners' => $propertyBannerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestapp_choice_property_banner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropertyBannerRepository $propertyBannerRepository, SluggerInterface $slugger): Response
    {
        $propertyBanner = new PropertyBanner();
        $form = $this->createForm(PropertyBannerType::class, $propertyBanner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on teste la présence d'un fichier sur l'input
            $banner =  $form->get('banner')->getData();
            if ($banner) {
                $originalFilename = pathinfo($banner->getClientOriginalName(), PATHINFO_FILENAME);
                // transformation du nom pour échapper les accents & autres
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$banner->guessExtension();

                // Déplacement du fichier dans le dossier recevant les fichiers SVG
                try {
                    $banner->move(
                        $this->getParameter('banners_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // Ajout dans l'entité le nom remanié
                $propertyBanner->setBannerFilename($newFilename);
            }

            $propertyBannerRepository->add($propertyBanner, true);

            $listbanners = $propertyBannerRepository->findAll();

            return $this->json([
                'code' => 200,
                'banner' => $propertyBanner->getName(),
                'valuebanner'=> $propertyBanner->getId(),
                'listbanners' => $this->renderView('gestapp/choice/property_banner/_listbanner.html.twig', [
                    'property_banners' => $listbanners
                ]),
                'message' => "Une nouvelle bannière a été ajoutée à la BDD."
            ], 200);
        }

        return $this->renderForm('gestapp/choice/property_banner/new.html.twig', [
            'property_banner' => $propertyBanner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_banner_show', methods: ['GET'])]
    public function show(PropertyBanner $propertyBanner): Response
    {
        return $this->render('gestapp/choice/property_banner/show.html.twig', [
            'property_banner' => $propertyBanner,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestapp_choice_property_banner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropertyBanner $propertyBanner, PropertyBannerRepository $propertyBannerRepository): Response
    {
        $form = $this->createForm(PropertyBannerType::class, $propertyBanner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyBannerRepository->add($propertyBanner, true);

            return $this->redirectToRoute('app_gestapp_choice_property_banner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestapp/choice/property_banner/edit.html.twig', [
            'property_banner' => $propertyBanner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestapp_choice_property_banner_delete', methods: ['POST'])]
    public function delete(Request $request, PropertyBanner $propertyBanner, PropertyBannerRepository $propertyBannerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propertyBanner->getId(), $request->request->get('_token'))) {
            $propertyBannerRepository->remove($propertyBanner, true);
        }

        return $this->redirectToRoute('app_gestapp_choice_property_banner_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del/{id}', name: 'app_gestapp_choice_property_banner_del', methods: ['POST'])]
    public function del(Request $request, PropertyBanner $propertyBanner, PropertyBannerRepository $propertyBannerRepository, ComplementRepository $complementRepository): Response
    {
        $complements = $complementRepository->findBy(['banner' => $propertyBanner]);
        foreach ($complements as $complement){
            $propertyBanner->removeComplement($complement);
        }

        $bannerFilename = $propertyBanner->getBannerFilename();
        if($bannerFilename){
            $pathheader = $this->getParameter('banners_directory').'/'.$bannerFilename;
            // On vérifie si l'image existe
            if(file_exists($pathheader)){
                unlink($pathheader);
            }
        }

        $propertyBannerRepository->remove($propertyBanner, true);

        $listBanners = $propertyBannerRepository->findAll();

        return $this->json([
            'code'=> 200,
            'message' => "La bannière a été correctement supprimée",
            'listbanner' => $this->renderView('gestapp/choice/property_banner/_listbanner.html.twig', [
                'property_banners' => $listBanners
            ])
        ], 200);
    }
}
