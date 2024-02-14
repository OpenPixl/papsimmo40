<?php

namespace App\Controller\Cart;

use App\Controller\Cart\CartItem;
use App\Repository\Cart\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function getCart() : array
    {
        return $this->session->get('cart', []);
    }

    protected function setCart(array $cart)
    {
        return $this->session->set('cart', $cart);
    }

    protected function saveCart(array $cart){
        $this->session->set('cart', $cart);
    }

    public function emptyCart(){
        $this->saveCart([]);
    }

    public function add(int $item, $product){

        $cart = $this->getCart();                                       // récupération du panier par le service CartService

        if(!array_key_exists($item, $cart)){                           // si dans le tableau panier si "Item" n'existe pas,
            $cart[$item]['Item'] = $item;
            $cart[$item]['ProductId'] = $product->getId();
            $cart[$item]['Qty'] = 1;                                   // alors le panier ajout 0 en quantité du panier,
        }

        $this->setCart($cart);                                         // on insére en session le panier modifié
    }

    public function increment(int $item, $product){

        $cart = $this->getCart();                                      // récupération du panier par le service CartService

        if(!array_key_exists($item, $cart)){                            // si dans le tableau panier si "Item" n'existe pas,
            return;
        }

        $cart[$item]['Qty']++;                                   // alors le panier ajout 0 en quantité du panier,
        $this->setCart($cart);                                          // on insére en session le panier modifié
    }

    public function decrement(int $item, $id, $uuid){

        // On chercher dans la session si le panier existe.
        // On creer si le panier n'existe pas.
        $cart = $this->getCart();

        if(!array_key_exists($item, $cart)){                              // On teste si dans le tableau panier si "Id" existe,
            return;                                                     // si c'est le cas ajoute la quantité,
        } else {
            if($cart[$item]['Qty'] === 1) {                                      // sinon, on ajoute 1 à l'Id dans le panier.
                $this->remove($item, $id, $uuid);
                return;
            }
            $cart[$item]['Qty']--;
        $this->session->set('cart', $cart);
        }

        $this->setCart($cart);
    }

    public function remove(int $item, int $id, $uuid)
    {
        $cart = $this->getCart();
        unset($cart[$item]);

        $this->setCart($cart);
    }

    public function getTotal()
    {
        $total = 0;
        foreach($this->getCart() as $item)
        {
            $id = $item['ProductId'];
            $product = $this->productRepository->find($id);
            $qty = $item['Qty'];
            if(!$product)
            {
                continue;                                           // ne force pas la boucle sur l'incrémentation du produit mais passe à l'item suivnat
            }
            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItem() : array
    {
        $detailedCart = [];                                           // on prépare un tableau du futur panier détaillé

        foreach($this->getCart() as $item)
        {
            $id = $item['ProductId'];
            $uuid = $item['CustomizeUuid'];
            $qty = $item['Qty'];
            $item = $item['Item'];

            $product = $this->productRepository->find($id);
            if(!$product)
            {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty, $item);
            //dd($detailedCart);
        }
        return $detailedCart;
    }
}