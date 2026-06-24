<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\choice\PropertyEnergy;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\choice\PropertyEnergyRepository;
use App\Repository\Gestapp\ComplementRepository;
use App\Repository\Gestapp\CustomerRepository;
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

    // Cette fonction est amenée à évoluée selon les besoins du développment
    #[Route('/admin/adaptbdd/entity', name: 'op_admin_adaptbdd_entity')]
    public function AdaptEntity(PropertyRepository $propertyRepository, EntityManagerInterface $em)
    {
        $properties = $propertyRepository->findAll();
        foreach($properties as $p){
            $reference = $p->getRef();
            $result = str_replace('/', '-', $reference);
            $p->setRef($result);

            $numDate = $p->getRefnumdate();
            $result = str_replace('/', '-', $numDate);
            $p->setRefnumdate($result);

            if($p->getDiagChoice() == 'non_obligatoire'){
                $p->setDiagChoice('non soumis');
            }

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

    #[Route('/admin/adaptbdd/withoutci', name: 'op_admin_adaptbdd_withoutci')]
    public function withoutci(CustomerRepository $customerRepository){

        $customers = $customerRepository->findAll();
        $lists = [];
        $customerswtCI = [];
        $customersCI = [];
        // liste des customers possédant une CI en BDD
        foreach ($customers as $customer){
            if (!empty($customer->getCifilename())) {
                $lists[] = $customer->getSlug();
            }

        }

        asort($lists);

        // Extraction des customers ou la CI est maquantes dans le répertoire
        foreach ($lists as $l){
            $customer = $customerRepository->findOneBy(['slug' => $l]);
            $customerFileCi = $customer->getCifilename();
            $slug = $customer->getSlug();
            $id = $customer->getId();
            $repertory = $slug."_".$id;
            $pathCustomer = $this->getParameter('customer_ci_directory').$repertory;
            if (!is_dir($pathCustomer)) {
                $customerswtCI[] = $customer->getId().' - '.$pathCustomer;
            }
            if (is_dir($pathCustomer)) {
                $customersCI[] = $customer->getId().' - '.$pathCustomer;
            }
        }

        dd($customerswtCI, $customersCI);

    }
}
