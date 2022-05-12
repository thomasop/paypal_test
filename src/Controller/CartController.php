<?php

namespace App\Controller;

use App\Handler\FormCartController;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /** @var FormCartController */
    private $formCartController;

    public function __construct(FormCartController $formCartController)
    {
        $this->formCartController = $formCartController;
    }

    #[route('/panier', name: 'cart_index')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function index(): Response
    {
        $total = $this->formCartController->index();
        return $this->render('cart/cart.html.twig', [
            'items' => $total[0],
            'total' => $total[1],
            'obj' => (object) $total[0],
        ]);
    }

    #[route('/panier/add/{id}', name: 'cart_add')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function add(int $id): Response
    {
        $this->formCartController->add($id);
        $this->addFlash(
            'success',
            'Produit ajouté au panier !'
        );
        return $this->redirectToRoute("cart_index");

    }

    #[route('/panier/addone/{id}', name: 'cart_addOne')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function addOne(int $id): Response
    {
        $this->formCartController->add($id);
        $this->addFlash(
            'success',
            'Quantité augmenté !'
        );
        return $this->redirectToRoute("cart_index");

    }

    #[route('/panier/remove/{id}', name: 'cart_remove', requirements: ["id" => "\d+"])]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function remove(int $id): Response
    {
        $this->formCartController->remove($id);
        $this->addFlash(
            'success',
            'Produit supprimé du panier !'
        );
        return $this->redirectToRoute("cart_index");
    }

    #[route('/panier/removeone/{id}', name: 'cart_removeOne', requirements: ["id" => "\d+"])]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function removeOne(int $id): Response
    {
        $this->formCartController->removeOne($id);
        $this->addFlash(
            'success',
            'Quantité diminué !'
        );
        return $this->redirectToRoute("cart_index");
    }
}
