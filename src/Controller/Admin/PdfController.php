<?php

namespace App\Controller\Admin;

use App\Entity\Gestapp\Property;
use App\Repository\Admin\ApplicationRepository;
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

    #[Route('/admin/pdf/Property/{id}', name: 'op_admin_pdf_property', methods: ['GET'])]
    public function FicheProperty(Property $property, PropertyRepository $propertyRepository, ApplicationRepository $applicationRepository, Pdf $knpSnappyPdf)
    {
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        $options = $property->getOptions();
        $equipments = $options->getPropertyEquipment();

        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);
        $html = $this->render('pdf/ficheproperty.html.twig', array(
            'property'  => $oneproperty,
            'equipments' => $equipments,
            'application' =>$application
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'files.pdf'
        );
    }
}
