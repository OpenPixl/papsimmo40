<?php

namespace App\Controller\Admin;

use App\Repository\Gestapp\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/opadmin/dellfiles')]
class DellfilesController extends AbstractController
{
    #[Route('/oldphotos/', name:'op_admin_dellfiles_oldsphotos')]
    public function OldPhotos(PhotoRepository $photoRepository) : response
    {
        $listOlds =  scandir($this->getParameter('property_photo_directory'),  SCANDIR_SORT_DESCENDING);

        $photos = $photoRepository->findAll();
        $listphotos = [];
        foreach ($photos as $photo){
            $name = $photo->getGaleryFrontName();
            array_push($listphotos, $name);
        }
        $list = array_merge($listphotos, $listOlds);
        array_pop($list);
        array_pop($list);

        $count = array_count_values($list);
        $results = array_filter($list, function ($value) use ($count) {
            return 1 === $count[$value];
        });
        $longarray = count($results);
        foreach ($results as $result){
            $pathname = $this->getParameter('property_photo_directory').'/'.$result;
            if(file_exists($pathname)){
                unlink($pathname);
            }
        }
        return $this->json([
            'message' => 'les '.$longarray.' photos orphelines ont été supprimées.'
        ]);

    }
}