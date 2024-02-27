<?php

namespace App\Controller\Cart;

use App\Entity\Cart\Product;
use App\Form\Cart\ProductType;
use App\Repository\Cart\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/cart/product')]
class ProductController extends AbstractController
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/', name: 'op_cart_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, RequestStack $requestStack): Response
    {
        $carts = $requestStack->getSession()->get('cart');
        //dd($carts);
        if($carts)
        {
            $detailedCart = $this->cartService->getDetailedCartItem();
            return $this->render('cart/product/index.html.twig', [
                'products' => $productRepository->findAll(),
                'items' => $detailedCart
            ]);
        }

        return $this->render('cart/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'op_cart_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('op_cart_product_new'),
            'method' => 'POST',
            'attr' => ['id' => 'formProduct']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout du visuel
            $VisuelFile = $form->get('visualFile')->getData();
            if ($VisuelFile) {
                $originalvisuelFileName = pathinfo($VisuelFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safevisuelFileName = $slugger->slug($originalvisuelFileName);
                $newvisuelFileName = $safevisuelFileName . '.' . $VisuelFile->guessExtension();
                $pathdir = $this->getParameter('property_product_directory');
                try {
                    if (is_dir($pathdir)) {
                        $VisuelFile->move(
                            $pathdir,
                            $newvisuelFileName
                        );
                    } else {
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir . "/", 0775, true);
                        // Déplacement de la photo
                        $VisuelFile->move(
                            $pathdir,
                            $newvisuelFileName
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $product->setVisualFilename($newvisuelFileName);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $products = $productRepository->findAll();

            return $this->json([
                'code' => 200,
                'Message' => "Le support a été correctement ajouté",
                'liste' => $this->renderView('cart/product/include/_liste.html.twig',[
                    'products' => $products
                ])
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
        return $this->json([
            "code" => 200,
            'showItem' => $this->renderView('cart/product/show.html.twig',[
                'product' => $product
            ])
        ], 200);
        //return $this->render('cart/product/show.html.twig', [
        //    'product' => $product,
        //]);
    }

    #[Route('/{id}/edit', name: 'op_cart_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, ProductRepository $productRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('op_cart_product_edit', [
                'id' => $product->getId()
            ]),
            'method' => 'POST',
            'attr' => ['id' => 'formProduct']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Bloc sur le visuel en cas de changement du visuel
            $VisuelFile = $form->get('visualFile')->getData();
            //dd($VisuelFile);
            if ($VisuelFile) {
                // Suppression de la photo si cette dernière est présente en BDD
                $visuelFileName = $product->getVisualFilename();
                if($visuelFileName){
                    $pathname = $this->getParameter('property_product_directory').'/'.$visuelFileName;
                    if(file_exists($pathname)){
                        unlink($pathname);
                    }
                }
                // Ajout de la nouvelle image
                $originalvisuelFileName = pathinfo($VisuelFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safevisuelFileName = $slugger->slug($originalvisuelFileName);
                $newvisuelFileName = $safevisuelFileName . '.' . $VisuelFile->guessExtension();
                $pathdir = $this->getParameter('property_product_directory');
                try {
                    if (is_dir($pathdir)) {
                        $VisuelFile->move(
                            $pathdir,
                            $newvisuelFileName
                        );
                    } else {
                        // Création du répertoire s'il n'existe pas.
                        mkdir($pathdir . "/", 0775, true);
                        // Déplacement de la photo
                        $VisuelFile->move(
                            $pathdir,
                            $newvisuelFileName
                        );
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $product->setVisualFilename($newvisuelFileName);
            }

            $entityManager->flush();

            $products = $productRepository->findAll();

            return $this->json([
                "code" => 200,
                "Message" => "Le support a été correctement ajouté",
                'liste' => $this->renderView('cart/product/include/_liste.html.twig',[
                    'products' => $products
                ])
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

    #[Route('/{id}', name: 'op_cart_product_del', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('op_cart_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/{id}', name: 'op_cart_product_delete', methods: ['POST'])]
    public function del(Request $request, Product $product, ProductRepository $productRepository, EntityManagerInterface $em)
    {
        $em->remove($product);
        $em->flush();

        $products = $productRepository->findAll();

        return $this->json([
            'code' => 200,
            'message' => 'Le supprot a été corretement retiré de la base de données.',
            'liste' => $this->renderView('cart/product/include/_liste.html.twig',[
                'products' => $products
            ])
        ], 200);
    }
}
