<?php

namespace App\Controller\Cart;

use App\Entity\Cart\Purchase;
use App\Repository\Cart\PurchaseItemRepository;
use App\Repository\Cart\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Admin\Employed;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Knp\Snappy\Pdf;
use Twig\Environment;
use Mpdf\Mpdf;

class PurchasesListController extends abstractController
{
    private Environment $twig;
    private Pdf $pdf;

    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }


    #[Route('/cart/purchases/', name:'op_cart_purchases_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté pour accéder à vos commandes")]
    public function index()
    {

        $member = $this->getUser();
        dd($member);

        return $this->render('cart/purchase/index.html.twig',[
            'purchases'=> $member->getPurchases(),
            'hide' => 0,
        ]);
    }

    #[Route('/cart/purchases/admin', name:'op_cart_purchases_admin', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté pour accéder à vos commandes")]
    public function listAdmin(PurchaseRepository $purchaseRepository, Request $request, PaginatorInterface $paginator)
    {
        $member = $this->getUser();

        $data = $purchaseRepository->findAll();
        $purchases = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            300
        );

        return $this->render('gestapp/purchase/list.html.twig',[
            'purchases'=> $purchases,
            'hide' => 0,
        ]);
    }

    #[Route('/cart/purchases/updateStatusPaid/{id}/{status}', name:'op_cart_purchases_updateStatePaid', methods: ['POST'])]
    public function updateStatePaidPurchase(Purchase $purchase, $status, MailerInterface $mailer, EntityManagerInterface $em)
    {
        // récupération des variables
        $numPurchase = $purchase->getNumPurchase();
        $member = $purchase->getCustomer();
        $emailMember = $member->getEmail();
        $fnMember = $member->getFirstName();
        $lsMember = $member->getLastName();

        // modification de l'entité en cours
        $purchase->setStatus($status);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($purchase);
        $entityManager->flush();

        // récupération de la liste de commandes pour son actualisation
        $purchases = $em->getRepository(Purchase::class)->findAll();

        // Envoi du mail de confirmation des fonds perçus pour la réalisation de la commande
        $email = (new TemplatedEmail())
            ->from('postmaster@openpixl.fr')
            ->to($emailMember)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Cartes de prières - Commande ' . $numPurchase . 'test')
            //->text('Sending emails is fun again!')
            ->htmlTemplate('email/Purchases/updatePurchaseState.html.twig')
            ->context([
                'author' => 'Soeur Marie',
                'commande' => $numPurchase,
                'prenomDestin' => $fnMember,
                'nomDestin' => $lsMember,
            ]);
        $mailer->send($email);

        // renvoie JSON à la page
        return $this->json([
            'code'          => 200,
            'message'       => "La commande a été correctement modifié.",
            'liste'         => $this->renderView('gestapp/purchase/include/_liste.html.twig', [
                'purchases' => $purchases,
                'hide' => 0
            ])
        ], 200);

    }

    #[Route('/cart/purchases/updateStatusPurchase/{id}/{status}', name:'op_cart_purchases_updateStatePurchase', methods: ['POST'])]
    public function updateStatusPurchase(Purchase $purchase, $status, MailerInterface $mailer)
    {
        // récupération des variables
        $numPurchase = $purchase->getNumPurchase();
        $member = $purchase->getCustomer();
        $emailMember = $member->getEmail();
        $fnMember = $member->getFirstName();
        $lsMember = $member->getLastName();

        // modification de l'entité en cours
        $purchase->setStatuspaid($status);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($purchase);
        $entityManager->flush();

        // récupération de la liste de commandes pour son actualisation
        $purchases = $this->getDoctrine()->getManager()->getRepository(Purchase::class)->findAll();

        // Envoi du mail de nouvelle recommandation au membre recommandé
        $email = (new TemplatedEmail())
            ->from('postmaster@openpixl.fr')
            ->to($emailMember)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Cartes de prières - Commande ' . $numPurchase . 'test')
            //->text('Sending emails is fun again!')
            ->htmlTemplate('email/Purchases/updatePurchasePaidState.html.twig')
            ->context([
                'author' => 'Soeur Marie',
                'commande' => $numPurchase,
                'prenomDestin' => $fnMember,
                'nomDestin' => $lsMember,
            ]);
        $mailer->send($email);

        // renvoie JSON à la page
        return $this->json([
            'code'          => 200,
            'message'       => "La commande a été correctement modifié.",
            'liste'         => $this->renderView('gestapp/purchase/include/_liste.html.twig', [
                'purchases' => $purchases,
                'hide' => 0
            ])
        ], 200);
    }

    /**
     * Affiche les nouvelles commandes sur le dashboard 
     * @Route("/op_admin/gestapp/purchases/byuserNew/{hide}", name="op_gestapp_purchases_byusernewpurchases", methods={"GET"})
     */
    #[Route('/cart/purchases/byuserNew/{hide}', name:'op_cart_purchases_byusernewpurchases', methods: ['GET'])]
    public function byUserReceiptNewPurchases($hide, PurchaseRepository $purchaseRepository): Response
    {
        $user = $this->getUser();
        $purchases = $purchaseRepository->findBy(['status'=>'PENDING']);
        return $this->render('gestapp/purchase/byuserReceipt.html.twig', [
            'purchases' => $purchases,
            'hide' => $hide,
        ]);
    }

    #[Route('/cart/purchases/byuserSend/{hide}', name:'op_cart_purchases_byusersend', methods: ['GET'])]
    public function byUserSend($hide,PurchaseRepository $purchaseRepository): Response
    {
        $user = $this->getUser();
        $purchases = $this->getDoctrine()->getRepository(Purchase::class)->findByUserSend($user);
        $hide = 1;
        return $this->render('gestapp/purchase/byuserSend.html.twig', [
            'purchases' => $purchases,
            'hide' => $hide
        ]);
    }

    #[Route('/cart/purchases/onePuchaseadmin/{commande}', name:'op_cart_purchases_onepurchaseadmin', methods: ['GET'])]
    public function onePurchaseAdmin($commande, PurchaseRepository $purchaseRepository, PurchaseItemRepository $purchaseItemRepository) : Response
    {
        $purchase = $purchaseRepository->onePurchase($commande);
        $purchase2 = $purchaseRepository->findOneBy(array('numPurchase' => $commande));
        $num = $purchase2->getId();
        //dd($idpurchase);
        $items = $purchaseItemRepository->itemsPurchase($num);
        //dd($items);

        return $this->render('gestapp/commande/show.html.twig', [
            'purchase'=>$purchase,
            'items' => $items
        ]);
    }

    #[Route('/cart/purchases/onePuchasepublic/{commande}', name:'op_cart_purchases_onepurchasepublic', methods: ['GET'])]
    public function onePurchasePublic($commande, PurchaseRepository $purchaseRepository, PurchaseItemRepository $purchaseItemRepository) : Response
    {
        $purchase = $purchaseRepository->onePurchase($commande);
        $purchase2 = $purchaseRepository->findOneBy(array('numPurchase' => $commande));
        $num = $purchase2->getId();
        //dd($idpurchase);
        $items = $purchaseItemRepository->itemsPurchase($num);
        //dd($items);

        return $this->render('gestapp/commande/showpublic.html.twig', [
            'purchase'=>$purchase,
            'items' => $items
        ]);
    }

    #[Route('/cart/purchases/onePurchase/{commande}', name:'op_cart_purchases_onepurchase', methods: ['GET'])]
    public function onePurchase($commande, PurchaseRepository $purchaseRepository, PurchaseItemRepository $purchaseItemRepository,Pdf $knpSnappyPdf) : Response
    {
        $purchase = $purchaseRepository->onePurchase($commande);
        $purchase2 = $purchaseRepository->findOneBy(array('numPurchase' => $commande));
        $num = $purchase2->getId();
        //dd($idpurchase);
        $items = $purchaseItemRepository->itemsPurchase($num);
        //dd($items);

        $mpdf = new Mpdf();
        $html = $this->renderView('pdf/purchases/onePurchaseFromCustomer.html.twig', array(
            'purchase'  => $purchase,
            'items' => $items
        ));

        $mpdf->WriteHTML($html);
        $mpdf->Output('fichier.pdf', 'D'); // Télécharger le fichier PDF

    }

    #[Route('/cart/purchases/delete/{commande}', name:'op_cart_purchases_delete', methods: ['POST'])]
    public function delPurchase(Purchase $purchase, PurchaseRepository $purchaseRepository, PurchaseItemRepository $purchaseItemRepository, EntityManagerInterface $em) : Response
    {
        $member = $this->getUser();

        $em->remove($purchase);
        $em->flush();

        return $this->json([
            'code'          => 200,
            'message'       => "La commande a été correctement supprimée.",
            'count'         => $this->renderView('gestapp/purchase/index.html.twig', [
                'purchases'=> $member->getPurchases(),
                'hide' => 0,
            ])
        ], 200);
    }
}