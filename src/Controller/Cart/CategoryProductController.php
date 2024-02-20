<?php

namespace App\Controller\Cart;

use App\Entity\Cart\CategoryProduct;
use App\Form\Cart\CategoryProductType;
use App\Repository\Cart\CategoryProductRepository;
use App\Repository\Cart\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/category/product')]
class CategoryProductController extends AbstractController
{
    #[Route('/', name: 'op_cart_category_product_index', methods: ['GET'])]
    public function index(CategoryProductRepository $categoryProductRepository): Response
    {
        return $this->render('cart/category_product/index.html.twig', [
            'category_products' => $categoryProductRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_cart_category_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $categoryProduct = new CategoryProduct();
        $form = $this->createForm(CategoryProductType::class, $categoryProduct, [
            'action' => $this->generateUrl('op_cart_category_product_new'),
            'method' => 'POST',
            'attr' => ['id' => 'formCatProduct']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoryProduct);
            $entityManager->flush();

            $products = $productRepository->findAll();

            //return $this->redirectToRoute('op_cart_category_product_index', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'code' => 200,
                'message' => 'La catégorie a correctement était ajouté à la base.',
                'liste' => $this->renderView('cart/product/include/_liste.html.twig',[
                    'products' => $products
                    ])
                ], 200);
        }

        $view = $this->render('cart/category_product/_form.html.twig', [
            'form' => $form,
            'categoryProduct' => $categoryProduct
        ]);

        return $this->json([
            'code' => 200,
            'message' => 'le formulaire est affiché',
            'formView' => $view->getContent()
        ]);
    }

    #[Route('/{id}', name: 'op_cart_category_product_show', methods: ['GET'])]
    public function show(CategoryProduct $categoryProduct): Response
    {
        return $this->render('cart/category_product/show.html.twig', [
            'category_product' => $categoryProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'op_cart_category_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryProduct $categoryProduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryProductType::class, $categoryProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('op_cart_category_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart/category_product/edit.html.twig', [
            'category_product' => $categoryProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'op_cart_category_product_delete', methods: ['POST'])]
    public function delete(Request $request, CategoryProduct $categoryProduct, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoryProduct->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categoryProduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_cart_category_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
