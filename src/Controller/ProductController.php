<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Handler\FormProductHandler;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProductController extends AbstractController
{
    /** @var FormProductHandler */
    private $formProductHandler;

    public function __construct(FormProductHandler $formProductHandler)
    {
        $this->formProductHandler = $formProductHandler;
    }

    #[Route('/', name: 'product')]
    public function index(ProductRepository $productRepository): Response
    {
        $product = $productRepository->findProducts();
        $productEntity = new Product();
        $form = $this->createForm(ProductType::class, $productEntity);
        if ($this->formProductHandler->add($form, $productEntity) === true) {
            
            $this->addFlash('success', 'Produit ajouté !');
            return $this->redirectToRoute('product');
        }
        return $this->render('product/index.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}', name: 'prod', requirements: ["id" => "\d+"])]
    #[ParamConverter('product', class: 'App\Entity\Product', options: ["mapping" => ["id" => "id"]])]
    public function product(Product $product): Response
    {
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }

    #[route('/produit/modification/{id}', name: 'prod_update')]
    #[IsGranted('ROLE_ADMIN', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function update(Product $product): Response
    {
        if (!$product) {
            throw $this->createNotFoundException('Pas de produit trouvé avec l\'id '.$product->getId());
        } else {
            $form = $this->createForm(ProductType::class, $product);
            if ($this->formProductHandler->update($form, $product) === true) {
                $this->addFlash(
                    'success',
                    'Produit modifié!'
                );
                return $this->redirectToRoute('prod', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('product/update.html.twig', [
                'form' => $form,
                'product' => $product
            ]);
        }
    }

    #[route('/produit/suppression/{id}', name: 'prod_delete')]
    #[IsGranted('ROLE_ADMIN', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function delete(Product $product, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->formProductHandler->delete($product);
            $this->addFlash(
                'success',
                'Produit supprimé!'
            );
            return $this->redirectToRoute('product');
        }
        return $this->redirectToRoute('product');
    }
}
