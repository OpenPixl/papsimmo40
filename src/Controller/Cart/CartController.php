<?php

namespace App\Controller\Cart;

use App\Entity\Cart\Cart;
use App\Form\Cart\CartConfirmationType;
use App\Repository\Cart\CartRepository;
use App\Repository\Cart\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    protected $productRepository;
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService, private RequestStack $requestStack,)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/cart/{id}', name: 'op_cart_cart_add', methods: ['GET', 'POST'], requirements: ['id'=>'\d+'])]
    public function cart($id, Request $request, EntityManagerInterface $em): Response
    {
        $json = intval($request->query->get('json'));

        // Récupération de l'objet produit
        $product = $this->productRepository->find($id);
        // teste si le produit existe dans la liste de produit.
        if(!$product){
            throw $this->createNotFoundException("Le produit portant l'identifiant $id n'existe pas.");
        }

        // Récupération des données du formulaire "ProductCustomize" et intégration dans la table
        $data = json_decode($request->getContent(), true);
        // On teste la présence du formulaire de personnalisation
        if($data){
            $idformat = $data['format'];
            $sessid = $this->requestStack->getSession()->getId();

            if(isset($data['name'])){
                $name = $data['name'];
            }else{
                $name = '';
            }
        }

        $cart = $this->cartService->getCart();
        // CONDITION :
        // Si le panier est vide : item à 0 et on ajoute la personnalisation
        // Sinon on boucle sur le panier pour identifier si un produit existe parmi les items.
        if(count($cart) == 0){
            $item = 0;
            $em->flush();
            $this->cartService->add($item, $product);
        }
        else {
            $item = 0;
            $exist = 0;
            foreach ($cart as $c){
                if($c['ProductId'] == $id){
                    $exist = 1;
                    $item = $c['Item'];
                }
            }
            // CONDITION :
            // Si le produit est absent : on l'ajoute au panier avec sa personnalisation.
            // Si le prdoduit existe : on récupère sa personnalisation et on incrémente la quantité
            if($exist == 0){
                $item = array_key_last($cart)+1;
                $em->flush();
                $this->cartService->add($item, $product);
            }else{
                if($request->query->has('item')){
                    // Récupération de la personnalisation
                    $parametres = $request->query->all();
                    $this->cartService->increment(intval($parametres['item']), $product);
                }else{
                    // Récupération de la personnalisation
                    $this->cartService->increment($item, $product);
                }
            }

        }
        //dd($cart);
        $this->addFlash('success', "Le produit a bien été ajouté au panier");

        if($request->query->get('returnToCart')){
            return $this->redirectToRoute('op_webapp_cart_showcartjson');
        }elseif($json === 1){
            return $this->redirectToRoute('op_cart_product_showmodalfooter',[
                'id' => $product->getId(),
            ]);
        }else{
            return $this->redirectToRoute('op_cart_product_index');
        }
    }

    #[Route('/cart/cart/duplicate/{id}/{uuid}/{item}', name: 'op_cart_cart_duplicate', methods: ['GET', 'POST'], requirements: ['id'=>'\d+'])]
    public function duplicate($id, $uuid, $item, Request $request, EntityManagerInterface $em): Response
    {
        $product = $this->productRepository->find($id);
        // 1. Création dun nouvel item pour le panier
        $cart = $this->cartService->getCart();
        $newitem = array_key_last($cart)+1;


        // 4. On ajoute le produit dupliqué directement dans le panier
        $this->cartService->add($newitem, $product);

        return $this->redirectToRoute('op_webapp_cart_showcartjson');
    }


    #[Route('/cart/cart/show', name: 'op_cart_cart_show', methods: ['GET', 'POST'])]
    public function showCart(Request $request, EntityManagerInterface $em, CartRepository $cartRepository)
    {
        $user = $this->getUser();
        $form = $this->createForm(CartConfirmationType::class);

        //Récupération de l'id de session et des personnalisation
        $session = $this->requestStack->getSession()->getId();
        //dd($session);
        $detailedCart = $this->cartService->getDetailedCartItem();

        //dd($detailedCart);

        foreach ($detailedCart as $d){
            //dd($d);
            // Construction des éléments nécessaire au panier
            $product = $d->product;

            //dd($session, $customization->getUuid());


            //dd($session, $customization, $this->cartService->getCart());

            $cart = new Cart();
            $cart->setUuid($session);
            $cart->setRefEmployed($user);
            $cart->setRefProduct($product);
            $cart->setProductId($product->getId());
            $cart->setProductName($product->getName());
            $cart->setproductCat($product->getCategory());
            $cart->setProductQty($d->qty);
            $cart->setItem($d->item);
            $em->persist($cart);
            $em->flush();
        }
        $carts = $cartRepository->findBy(['uuid'=> $session]);
        //dd($carts);
        $cartspanel = $carts;
        foreach($carts as $cart){
            $em->remove($cart);
            $em->flush();
        }
        //dd($cartspanel);

        return $this->render('cart/cart/index.html.twig', [
            'carts' => $cartspanel,
            'session' => $session,
            'user' => $user,
            'confirmationForm' => $form->createView()
        ]);
    }

    // Liste les produits inclus dans le panier
    #[Route("/webapp/cart/showjson", name:"op_webapp_cart_showcartjson", methods: ['GET'])]
    public function showCartJson(Request $request, EntityManagerInterface $em, CartRepository $cartRepository)
    {
        $form = $this->createForm(CartConfirmationType::class);
        $user = $this->getUser();

        //Récupération de l'id de session et des personnalisation
        $session = $this->get('session')->getId();

        $detailedCart = $this->cartService->getDetailedCartItem();

        foreach ($detailedCart as $d){
            //dd($d);
            // Construction des éléments nécessaire au panier
            $product = $d->product;
            $customization = $d->productCustomize;

            //dd($session, $customization->getUuid());

            if($session != $customization->getUuid()){
                $customization->setUuid($session);
                $em->persist($customization);
                $em->flush();
                $this->cartService->updateUuid($d->item, $customization);
            }

            //dd($session, $customization, $this->cartService->getCart());

            $cart = new Cart();
            $cart->setProductId($product->getId());
            $cart->setProduct($product);
            $cart->setProductName($product->getName());
            $cart->setProductNature($product->getProductNature());
            $cart->setproductCategory($product->getProductCategory());
            $cart->setProductQty($d->qty);
            $cart->setProductRef($product->getRef());
            $cart->setCustomId($customization->getId());
            $cart->setCustomIdformat($customization->getFormat()->getId());
            $cart->setCustomFormat($customization->getFormat()->getName());
            $cart->setCustomName($customization->getName());
            $cart->setCustomPrice($customization->getFormat()->getPriceformat());
            $cart->setCustomWeight($customization->getFormat()->getWeight());
            $cart->setItem($d->item);
            $cart->setUuid($customization->getUuid());
            $em->persist($cart);
            $em->flush();
        }
        $carts = $cartRepository->findBy(['uuid'=> $session]);
        $cartspanel = $carts;
        foreach($carts as $cart){
            $em->remove($cart);
            $em->flush();
        }

        //dd($cartspanel);

        // Retourne une réponse en json
        return $this->json([
            'code'          => 200,
            'message'       => "Le produit a été correctement supprimé.",
            'liste'         => $this->renderView('gestapp/cart/include/_liste.html.twig', [
                'carts' => $cartspanel,
                'session' => $session,
                'user' => $user,
                'confirmationForm' => $form->createView()
            ])
        ], 200);
    }

    // Liste les produits inclus dans le panier
    #[Route("/webapp/cart/showcartcount/{id}", name:"op_gestapp_cart_showcartcount")]
    public function showcartcount($id, Request $request, EntityManagerInterface $em, CartService $cartService)
    {
        $detailedCart = $this->cartService->getDetailedCartItem();
        $product = $this->productRepository->find($id);
        $session = $request->getSession()->get('name_uuid');

        // Retourne une réponse en json
        return $this->json([
            'code'          => 200,
            'message'       => "Le produit a été correctement ajouté.",
        ], 200);
    }

    // Décrémentation du panier
    #[Route("/cart/cart/decrement/{id}", name: "op_cart_cart_decrement", requirements: ["id"=>"\d+"], methods: ['POST'])]
    public function decrementeCart($id,  Request $request): Response
    {
        $json = intval($request->query->get('json'));
        $product = $this->productRepository->find($id);

        // teste si le produit existe dans la liste de produit.
        if(!$product){
            throw $this->createNotFoundException("Le produit portant l'identifiant $id n'existe pas et ne peut être diminué dans le panier.");
        }

        $cart = $this->cartService->getCart();

        if($request->query->has('item')){
            $parametres = $request->query->all();
            $this->cartService->decrement(intval($parametres['item']), $id);
        }else{
            $item = 0;
            foreach ($cart as $c){
                //dd($c['Product']);
                if($c['ProductId'] == $id){
                    $item = $c['Item'];
                }
            }
            $this->cartService->decrement($item, $id);
        }

        $this->addFlash('success', "Le produit a bien été diminué dans le panier.");

        if($request->query->get('returnToCart')){
            return $this->redirectToRoute('op_webapp_cart_showcartjson');
        }
        elseif($json === 1){
            return $this->redirectToRoute('op_cart_product_showmodalfooter',[
                'id' => $product->getId(),
            ]);
        }

        return $this->redirectToRoute('op_cart_product_index');
    }

    // Suppression produit du panier
    #[Route("/webapp/cart/{idProduct}/del/{item}", name: "op_cart_cart_delete", requirements: ["id"=>"\d+"], methods: ['POST'])]
    public function deleteProduct(
        $idProduct,
        $item,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        CartService $cartService,
        EntityManagerInterface $em)
    {

        // Suppression de la ligne
        $this->cartService->remove(intval($item), $idProduct);

        // récupération des elements du panier
        $session = $this->requestStack->getSession()->getId();
        $user = $this->getUser();
        $detailedCart = $this->cartService->getDetailedCartItem();

        foreach ($detailedCart as $d){
            // Construction des éléments nécessaire au panier
            $product = $d->product;

            $cart = new Cart();
            $cart->setUuid($session);
            $cart->setRefEmployed($user);
            $cart->setRefProduct($product);
            $cart->setProductId($product->getId());
            $cart->setProductName($product->getName());
            $cart->setproductCat($product->getCategory());
            $cart->setProductQty($d->qty);
            $cart->setItem($d->item);
            $em->persist($cart);
            $em->flush();
        }
        $carts = $cartRepository->findBy(['uuid'=> $session]);
        $cartspanel = $carts;
        foreach($carts as $cart){
            $em->remove($cart);
            $em->flush();
        }

        return $this->json([
            'code' => 200,
            'message' => 'Le produit a été correctement retiré du panier',
            'liste'         => $this->renderView('cart/cart/include/_liste.html.twig', [
                'carts' => $cartspanel,
                'session' => $session,
                'user' => $user,
            ])
        ], 200);

    }

    #[Route('/cart/cart/delcheckboxes/', name: 'op_cart_cart_delcheckboxes', methods: ['POST'])]
    public function delChexboxes(Request $request, CartService $cartService, CartRepository $cartRepository, EntityManagerInterface $em)
    {
        $arrayCheckboxes = json_decode($request->getContent());

        foreach($arrayCheckboxes as $array){
            $item = explode("-", $array)[0];
            $id = explode("-", $array)[1];
            $this->cartService->remove(intval($item), $id);
        }
        $session = $this->requestStack->getSession()->getId();
        $user = $this->getUser();
        $detailedCart = $this->cartService->getDetailedCartItem();

        foreach ($detailedCart as $d){
            // Construction des éléments nécessaire au panier
            $product = $d->product;

            $cart = new Cart();
            $cart->setUuid($session);
            $cart->setRefEmployed($user);
            $cart->setRefProduct($product);
            $cart->setProductId($product->getId());
            $cart->setProductName($product->getName());
            $cart->setproductCat($product->getCategory());
            $cart->setProductQty($d->qty);
            $cart->setItem($d->item);
            $em->persist($cart);
            $em->flush();
        }
        $carts = $cartRepository->findBy(['uuid'=> $session]);
        $cartspanel = $carts;
        foreach($carts as $cart){
            $em->remove($cart);
            $em->flush();
        }

        return $this->json([
            'code' => 200,
            'message' => 'Les produits ont été correctement retirés du panier',
            'liste'         => $this->renderView('cart/cart/include/_liste.html.twig', [
                'carts' => $cartspanel,
                'session' => $session,
                'user' => $user,
            ])
        ], 200);
    }

}
