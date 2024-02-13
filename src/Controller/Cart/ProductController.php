<?php

namespace App\Controller\Cart;

use App\Entity\Cart\Product;
use App\Form\Cart\ProductType;
use App\Repository\Cart\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'op_cart_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('cart/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_cart_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('op_cart_product_new'),
            'method' => 'POST',
            'attr' => ['class' => 'formProduct']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $products = $productRepository->findAll();

            return $this->json([
                "code" => 200,
                "Message" => "Le support a été correctement ajouté"
            ], 200);

            //return $this->redirectToRoute('op_cart_product_index', [], Response::HTTP_SEE_OTHER);
        }

        $view = $this->render('cart/product/_form.html.twig', [
            'product' => $product,
            'form' => $form
        ]);

        return $this->json([
            "code" => 200,
            "formView" => $view->getContent()
        ], 200);

        //return $this->render('cart/product/new.html.twig', [
        //    'product' => $product,
        //    'form' => $form,
        //]);
    }

    #[Route('/{id}', name: 'op_cart_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('cart/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_cart_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('op_cart_product_new', [
                'id' => $product->getId()
            ]),
            'method' => 'POST',
            'attr' => ['class' => 'formProduct']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $products = $productRepository->findAll();

            return $this->json([
                "code" => 200,
                "Message" => "Le support a été correctement ajouté"
            ], 200);

            //return $this->redirectToRoute('op_cart_product_index', [], Response::HTTP_SEE_OTHER);
        }

        $view = $this->render('cart/product/_form.html.twig', [
            'product' => $product,
            'form' => $form
        ]);

        return $this->json([
            "code" => 200,
            "formView" => $view->getContent()
        ], 200);

        //return $this->render('cart/product/edit.html.twig', [
        //    'product' => $product,
        //    'form' => $form,
        //]);
    }

    #[Route('/{id}', name: 'op_cart_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_cart_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
