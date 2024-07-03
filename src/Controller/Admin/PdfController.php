<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Employed;
use App\Entity\Admin\PdfRendered;
use App\Entity\Gestapp\Customer;
use App\Entity\Gestapp\Property;
use App\Entity\Webapp\Articles;
use App\Form\Admin\QrcodeType;
use App\Repository\Admin\ApplicationRepository;
use App\Repository\Admin\EmployedRepository;
use App\Repository\Admin\PdfRenderedRepository;
use App\Repository\Gestapp\CustomerRepository;
use App\Repository\Gestapp\PhotoRepository;
use App\Repository\Gestapp\PropertyRepository;
use App\Repository\Webapp\ArticlesRepository;
use App\Service\QrcodeService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
        $html = 0; // variable pour basculer du mode pdf au mode html
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        //dd($property);
        $options = $property->getOptions();
        $equipments = $options->getPropertyEquipment();
        $firstphoto = $photoRepository->firstphoto($property->getId());
        // Récupération des photos correspondantes au bien
        $photos = $photoRepository->findBy(['property'=>$property->getId()], ['position' => 'ASC']);
        $otheroptions = $options->getPropertyOtheroption();
        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);

        //dd($photos);

        if($html==1){
            //dd($oneproperty);
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
                'otheroptions' => $otheroptions,
                'application' =>$application,
                'firstphoto' => $firstphoto,
                'photos' => $photos,

            ));

            return new PdfResponse(
                $knpSnappyPdf
                    ->setOption("enable-local-file-access",true
                    )
                    ->getOutputFromHtml($html),
                'Fiche'.$property->getRefMandat().'-A4Portrait.pdf'
            );
        }
    }

    #[Route('/admin/pdf/Property/ficheagencepaysage/{id}', name: 'op_admin_pdf_ficheagencepaysage', methods: ['GET'])]
    public function FicheAgencePaysage(Property $property, PropertyRepository $propertyRepository, ApplicationRepository $applicationRepository, Pdf $knpSnappyPdf, PhotoRepository $photoRepository)
    {
        $html = 0; // variable pour basculer du mode pdf au mode html
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        //dd($property);
        $options = $property->getOptions();
        $equipments = $options->getPropertyEquipment();
        $threephotos = $photoRepository->threephotos($property->getId(), 3);
        // Récupération des photos correspondantes au bien
        $otheroptions = $options->getPropertyOtheroption();
        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);

        if($html==1){
            return $this->render(
                'pdf/fichepropertypaysage2.html.twig', array(
                'property'  => $oneproperty,
                'equipments' => $equipments,
                'otheroptions' => $otheroptions,
                'application' =>$application,
                'threephotos' => $threephotos,
            ));
        }else{
            $html = $this->twig->render('pdf/fichepropertypaysage2.html.twig', array(
                'property'  => $oneproperty,
                'equipments' => $equipments,
                'otheroptions' => $otheroptions,
                'application' =>$application,
                'threephotos' => $threephotos,
            ));

            return new PdfResponse(
                $knpSnappyPdf
                    ->setOption("enable-local-file-access",true)
                    ->setOption("orientation", 'Landscape')
                    ->getOutputFromHtml($html),
                'Fiche'.$property->getRefMandat().'-A4Paysage.pdf'
            );
        }
    }

    #[Route('/admin/pdf/Property/ficheagenceportrait/{id}', name: 'op_admin_pdf_ficheagenceportrait', methods: ['GET'])]
    public function FicheAgencePortrait(Property $property, PropertyRepository $propertyRepository, ApplicationRepository $applicationRepository, Pdf $knpSnappyPdf, PhotoRepository $photoRepository)
    {
        $html = 0; // variable pour basculer du mode pdf au mode html
        $oneproperty = $propertyRepository->oneProperty($property->getId());
        //dd($property);
        $options = $property->getOptions();
        $equipments = $options->getPropertyEquipment();
        $firstphoto = $photoRepository->firstphoto($property->getId());
        $threephotos = $photoRepository->threephotos($property->getId(), 4);
        // Récupération des photos correspondantes au bien
        $photos = $photoRepository->findBy(['property'=>$property->getId()], ['position' => 'ASC']);
        $otheroptions = $options->getPropertyOtheroption();
        $application = $applicationRepository->findOneBy([], ['id'=>'DESC']);

        if($html==1){
            return $this->render(
                'pdf/ficheproperty2.html.twig', array(
                'property'  => $oneproperty,
                'equipments' => $equipments,
                'otheroptions' => $otheroptions,
                'application' =>$application,
                'firstphoto' => $firstphoto,
                'threephotos' => $threephotos,
            ));
        }else{
            $html = $this->twig->render('pdf/ficheproperty2.html.twig', array(
                'property'  => $oneproperty,
                'equipments' => $equipments,
                'otheroptions' => $otheroptions,
                'application' =>$application,
                'firstphoto' => $firstphoto,
                'threephotos' => $threephotos,
            ));

            return new PdfResponse(
                $knpSnappyPdf
                    ->setOption("enable-local-file-access",true)
                    //->setOption("orientation", 'Landscape')
                    ->getOutputFromHtml($html),
                'Fiche'.$property->getRefMandat().'_client-A4Portrait.pdf'
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

    #[Route('/admin/pdf/articletopdf/{id}', name: 'op_admin_pdf_articletopdf', methods: ['POST'])]
    public function ArticleToPdf(Articles $article, Pdf $knpSnappyPdf, PdfRenderedRepository $pdfRenderedRepository)
    {
        $slug = $article->getSlug();
        $pdfRendered = $pdfRenderedRepository->findOneBy(['name' => $slug]);
        $filename = 'pdf/articles/'.$slug.'.pdf';
        $header = $this->renderView('pdf/include/header.html.twig');
        $footer = $this->renderView('pdf/include/footer.html.twig');

        if(!$pdfRendered)
        {
            // hydration d'une nouvelle entité pdfRendered
            $newPdf = new PdfRendered();
            $newPdf->setName($slug);
            $newPdf->setFilename($filename);
            $pdfRenderedRepository->add($newPdf, true);
            // Génération du fichiers Pdf & stockage dans le dossier "pdf/articles"
            $knpSnappyPdf->setOption("footer-html", $footer);
            $knpSnappyPdf->setOption("header-html", $header);
            $knpSnappyPdf->setOption("encoding","UTF-8");
            $knpSnappyPdf->generateFromHtml(
                $this->renderView(
                    'webapp/articles/articletopdf.html.twig',
                    array(
                        'article'  => $article
                    )
                ),
                'pdf/articles/'.$article->getSlug().'.pdf'
            );
            // Enregistrement en BDD
            $pdfRenderedRepository->add($newPdf, true);
            // Retour JSON
            return $this->json([
                'code'=> 200,
                'message' => "Le fichier PDF à été généré.",
                'lien' => $this->renderView('admin/pdf_rendered/articletopdf.html.twig',[
                    'pdfrendered' => $pdfRendered,
                    'article' => $article
                ])
            ], 200);
        }else{
            $url = $pdfRendered->getFilename();

            if(file_exists($url)){
                unlink($url);
            }

            // Génération du fichiers Pdf & stockage dans le dossier "pdf/articles"
            $knpSnappyPdf->setOption("footer-html", $footer);
            $knpSnappyPdf->setOption("header-html", $header);
            $knpSnappyPdf->setOption("encoding","UTF-8");
            $knpSnappyPdf->generateFromHtml(
                $this->renderView(
                    'webapp/articles/articletopdf.html.twig',
                    array(
                        'article'  => $article
                    )
                ),
                'pdf/articles/'.$article->getSlug().'.pdf'
            );

            // Actualisation en BDD
            $pdfRenderedRepository->add($pdfRendered, true);

            return $this->json([
                'code'=> 200,
                'message' => "Le fichier PDF à été généré.",
                'lien' => $this->renderView('admin/pdf_rendered/articletopdf.html.twig',[
                    'pdfrendered' => $pdfRendered,
                    'article' => $article
                ])
            ], 200);
        }
    }

    #[Route('/admin/pdf/qrcodeproperty/{idproperty}', name: 'op_admin_pdf_qrcodeproperty', methods: ['POST', 'GET'])]
    public function generateQrcodeProperty(
        Request $request,
        QrcodeService $qrcodeService,
        $idproperty,
        PropertyRepository $propertyRepository,
        EntityManagerInterface $em
    ): Response
    {
        $qrCode = null;
        $property = $propertyRepository->find($idproperty);

        $qrCode = $qrcodeService->qrcodeOneProperty($property);

        $property->setQrcodeUrl($qrCode);
        $em->persist($property);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Le QrCode a été correctement généré',
            'vueQr' => $this->renderView('gestapp/photo/include/qrcode.html.twig',[
                'property' => $property,
            ])
        ],200);
    }

}
