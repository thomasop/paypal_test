<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Handler\FormOrderHandler;
use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderController extends AbstractController
{
    /** @var TokenStorageInterface */
    private $tokenStorageInterface;
    /** @var SessionInterface */
    private $session;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var FormOrderHandler */
    private $formOrderHandler;

    public function __construct(TokenStorageInterface $tokenStorageInterface, SessionInterface $session, OrderRepository $orderRepository, FormOrderHandler $formOrderHandler)
    {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->session = $session;
        $this->orderRepository = $orderRepository;
        $this->formOrderHandler = $formOrderHandler;
    }

    #[Route('/order', name: 'app_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function index(): Response
    {
        $panier = $this->session->get('panier');
        if (true === $this->formOrderHandler->index($panier)) {
            return $this->redirectToRoute('display_order', ['id' => $this->tokenStorageInterface->getToken()->getUser()]);
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/order/user/{id}', name: 'display_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function display(User $user): Response
    {
        $order = $this->formOrderHandler->display($user);

        return $this->render('order/index.html.twig', [
            'orders' => $order[0],
            'total' => $order[1],
        ]);
    }

    #[Route('/order/history/user/{id}', name: 'display_order_history')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function history(User $user): Response
    {
        return $this->render('order/history.html.twig', [
            'orders' => $this->orderRepository->findBy(['user' => $user, 'status' => false]),
        ]);
    }

    #[Route('/orders/delete/{id}', name: 'delete_all_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function deleteAll(User $user): Response
    {
        $this->formOrderHandler->deleteAll($user);
        $this->addFlash(
            'success',
            'Commande en cours supprimé !'
        );

        return $this->redirectToRoute('product');
    }

    #[Route('/orders/accept/{id}', name: 'accept_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function accept(User $user): Response
    {
        $this->formOrderHandler->accept($user);
        $this->addFlash(
            'success',
            'Paiement accepté !'
        );

        return $this->redirectToRoute('display_order_history', [
            'id' => $user,
        ]);
    }

    #[Route('/order/addone/{id}/{order}', name: 'addone_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function addOne(User $user, Order $order): Response
    {
        if ($user === $order->getUser()) {
            $this->formOrderHandler->addOne($order);
            $this->addFlash(
                'success',
                'Produit : '.$order->getProduct().' => quantité augmenté !'
            );

            return $this->redirectToRoute('display_order', [
                'id' => $user,
            ]);
        }

        return $this->redirectToRoute('product');
    }

    #[Route('/order/delete/{id}/{order}', name: 'delete_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function delete(User $user, Order $order): Response
    {
        if ($user === $order->getUser()) {
            $this->formOrderHandler->delete($order);
            $this->addFlash(
                'success',
                'Produit supprimé de la commande en cours !'
            );

            return $this->redirectToRoute('display_order', [
                'id' => $user,
            ]);
        }

        return $this->redirectToRoute('product');
    }

    #[Route('/order/removeone/{id}/{order}', name: 'removeone_order')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas accès à cette page')]
    public function removeOne(User $user, Order $order): Response
    {
        if ($user === $order->getUser()) {
            $this->formOrderHandler->removeOne($order);
            $this->addFlash(
                'success',
                'Produit : '.$order->getProduct().' => quantité diminué !'
            );

            return $this->redirectToRoute('display_order', [
                'id' => $user,
            ]);
        }

        return $this->redirectToRoute('product');
    }
}
