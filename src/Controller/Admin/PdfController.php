<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Property;
use App\Repository\Admin\ApplicationRepository;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class PdfController extends AbstractController
{
    private Environment $twig;
    private Pdf $pdf;

    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    #[Route('/admin/pdf/Property/fiche/{id}', name: 'op_admin_pdf_property', methods: ['GET'])]
    public function FicheProperty(Property $property, PropertyRepository $propertyRepository, ApplicationRepository $applicationRepository, Pdf $knpSnappyPdf, PhotoRepository $photoRepository)
    {
        $html = 1;
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        $options = $property->getOptions();
        $equipments = $options->getPropertyEquipment();
        $firstphoto = $photoRepository->firstphoto($property->getId());
        //dd($firstphoto);
        // Récupération des photos correspondantes au bien
        $photos = $photoRepository->findBy(['property'=>$property->getId()]);
        $otheroptions = $options->getPropertyOtheroption();

        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);

        //dd($firstphoto);

        if($html==1){
            return $this->render(
                'pdf/ficheproperty.html.twig', array(
                'property'  => $oneproperty,
                'equipments' => $equipments,
                'otheroptions' => $otheroptions,
                'application' =>$application,
                'firstphoto' => $firstphoto,
                'photos' => $photos
            ));
        }else{
            $html = $this->twig->render('pdf/ficheproperty.html.twig', array(
                'property'  => $oneproperty,
                'equipments' => $equipments,
                'options' => $options,
                'application' =>$application,
                'firstphoto' => $firstphoto,
                'photos' => $photos
            ));

            return new PdfResponse(
                $knpSnappyPdf
                    ->setOption("enable-local-file-access",true
                    )
                    ->getOutputFromHtml($html),
                'files.pdf'
            );
        }




    }

    #[Route('/admin/pdf/Property/dip/{id}', name: 'op_admin_pdf_dip', methods: ['GET'])]
    public function dip(
        Property $property,
        PropertyRepository $propertyRepository,
        Pdf $knpSnappyPdf,
        ApplicationRepository $applicationRepository,
        Customer $customer,
        CustomerRepository $customerRepository,
        EmployedRepository $employedRepository
        )
    {
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        $customers = $customerRepository->CustomerForProperty($property->getId());
        $options = $property->getOptions();
        $equipments = $options->getPropertyEquipment();
        $refemployed = $property->getRefEmployed();
        $commercial = $employedRepository->find($refemployed);
        //$customers = $customerRepository->findBy(['properties', $property->getId()]);

        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);
        $html = $this->twig->render('pdf/Précontrat_signMandat.html.twig', [
            'property'  => $oneproperty,
            'equipments' => $equipments,
            'application' =>$application,
            'commercial' => $commercial,
            'customers' => $customers
             //'customers' => $customers
        ]);

        return new PdfResponse(

            $knpSnappyPdf
                ->setOption("enable-local-file-access",true
                )
                ->getOutputFromHtml($html),
            'files.pdf'
        );
    }

    #[Route('/admin/pdf/Property/MandatVente/{id}', name: 'op_admin_pdf_MandatVente', methods: ['GET'])]
    public function MandatVente(
        Property $property,
        PropertyRepository $propertyRepository,
        ApplicationRepository $applicationRepository,
        CustomerRepository $customerRepository,
        Pdf $knpSnappyPdf,
    )
    {
        // récupérer les données correspondant au bien
        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        $customers = $customerRepository->CustomerForProperty($property->getId());
        //dd($customers);

        //return $this->render('pdf/MandatVente.html.twig', [
        //    'propriete'  => $oneproperty,
        //    'application' =>$application,
        //     'customers' => $customers
        //     //'customers' => $customers
        //]);

        $html = $this->twig->render('pdf/MandatVente.html.twig', [
            'propriete'  => $oneproperty,
            'application' =>$application,
            'customers' => $customers
            //'customers' => $customers
        ]);

        return new PdfResponse(
            $knpSnappyPdf
                ->setOption("enable-local-file-access",true
                )
                ->setOption("margin-left", 20)
                ->setOption("margin-right", 20)
                ->getOutputFromHtml($html),
            'files.pdf'
        );

    }
}
