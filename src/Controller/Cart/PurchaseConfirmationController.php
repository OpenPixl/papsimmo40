<?php

namespace App\Controller\Cart;

use App\Entity\Cart\Purchase;
use App\Entity\Cart\PurchaseItem;
use App\Form\Gestapp\CartConfirmationType;
use App\Repository\Cart\PurchaseItemRepository;
use App\Repository\Cart\PurchaseRepository;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseConfirmationController extends AbstractController
{

    protected $cartService;
    protected $em;
    private $requestStack;


    public function __construct(CartService $cartService, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    #[Route('/cart/purchase/confirm', name: 'op_cart_purchase_confirm', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER", message:"Vous devez être inscrit sur la plateforme pour confirmer votre commande")]
    public function confirm(Request $request, EntityManagerInterface $em, PurchaseRepository $purchaseRepository)
    {
        $user = $this->getUser();
        $lastPurchase = $purchaseRepository->findLastRef();
        if(!$lastPurchase){
            $numPurchase = 'Comm-' . 1;
        }else{
            $lastPurchase = explode('-', $lastPurchase->getNumPurchase());
            $numPurchase = $lastPurchase[1]++;
        }

        $cartItems = $this->cartService->getDetailedCartItem();
        if(count($cartItems) === 0){
            $this->addFlash('warning', 'le panier est vide, impossible de commander');
            return $this->redirectToRoute('op_cart_product_index');
        }

        //dd($this->cartService->getTotal());
        // contruction du numero de commande

        $purchase = new Purchase;
        $purchase
            ->setRefEmployed($user)
            ->setNumPurchase($numPurchase)
            ->setStatus("PENDING")
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        $totalItem = 0;
        foreach($this->cartService->getDetailedCartItem() as $cartItem){
            //récupération des personnalisation du produit
            $purchaseItem = new PurchaseItem;
            $purchaseItem
                ->setPurchase($purchase)
                ->setProductRef($cartItem->product->getRef())
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setPropertyVisualFilename($cartItem->product->getVisualFilename())
                ->setProductQty($cartItem->qty)
                ->setTotalItem($cartItem->qty)
            ;
            $this->em->persist($purchaseItem);

            $totalItem = $totalItem + $purchaseItem->getTotalItem();
        }

        $purchase->setTotalItem($totalItem);
        $this->em->flush();
        $this->cartService->emptyCart();
        $this->addFlash('success', "La commande est enregistré");


        $this->em->flush();

        // Renouvellement de la session
        $session = $this->requestStack->getSession();
        $session->migrate();

        return $this->json([
            'code' => 200,
            'message' => "La commande a été envoyé à l'administrateur.Vous pouvez quittez cette page."
        ]);

        //return $this->redirectToRoute('op_cart_product_index');
    }
}