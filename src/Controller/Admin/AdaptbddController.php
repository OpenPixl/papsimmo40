<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Service\PropertyService;
use App\Service\Transfert\TransfertPhotos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdaptbddController extends AbstractController
{
    #[Route('/admin/adaptbdd', name: 'op_admin_adaptbdd_index')]
    public function index(): Response
    {
        return $this->render('admin/adaptbdd/index.html.twig', [
            'controller_name' => 'AdaptbddController',
        ]);
    }

    #[Route('/admin/adaptbdd/entity', name: 'op_admin_adaptbdd_entity')]
    public function AdaptEntity(EmployedRepository $employedRepository, EntityManagerInterface $em)
    {
        $complements = $employedRepository->findAll();
        foreach($complements as $c){
            $name = $c->getFirstName();
            $c->setFirstName($name);
            $em->flush();
        }

        return $this->json(['message' => 'Mise à jour BDD effectuée.'],200);
    }
    #[Route('/admin/adaptbdd/renameFiles', name: 'op_admin_adaptbdd_renamefiles')]
    public function renameFiles(
        PropertyRepository $propertyRepository,
        PhotoRepository $repository,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        TransfertPhotos $transfertPhotos,
        PropertyService $propertyService,
    )
    {
        $properties = $propertyRepository->findAll();
        foreach($properties as $p){
            $photos = $repository->findBy(['property' => $p->getId()], ['position' => 'ASC']);;
            if($photos){
                for ($i = 0; $i < count($photos); $i++) {
                    $photo = $photos[$i];
                    $filename = $photo->getGaleryFrontName();
                    $path = $photo->getPath();
                    $oldPath = $this->getParameter('property_photo_directory')."/".$path.'/'.$filename;
                    $nameApp = $transfertPhotos->getName($p);
                    $numMandat = $propertyService->getMandat($p);
                    $newname = $nameApp.'-'.$numMandat.'-'.uniqid();
                    $newPath = $this->getParameter('property_photo_directory')."/".$path.'/'.$newname;
                    if(file_exists($oldPath)){
                        rename($oldPath, $newPath);
                        $photo->setGaleryFrontName($newname);
                        $em->flush();
                    }
                }
            }
        }

        return $this->json(['code'=> 200]);

    }

    #[Route('/admin/adaptbdd/renameDirectory', name: 'op_admin_adaptbdd_renameDirectory')]
    public function renameDirectory(PropertyRepository $propertyRepository){

        $properties = $propertyRepository->findAll();

    }
}
