<?php

namespace App\Handler;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Tool\EntityManager;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FormOrderHandler
{
    /** @var ProductRepository */
    private $productRepository;
    /** @var TokenStorageInterface */
    private $tokenStorageInterface;
    /** @var EntityManager */
    private $entityManager;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var FormCartController */
    private $formCartController;

    public function __construct(ProductRepository $productRepository, TokenStorageInterface $tokenStorageInterface, EntityManager $entityManager, OrderRepository $orderRepository, FormCartController $formCartController)
    {
        $this->productRepository = $productRepository;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->formCartController = $formCartController;
    }

    public function index(array $panier): bool
    {
        if (!empty($panier)) {
            foreach ($panier as $key => $value) {
                $result = $this->productRepository->findBy(['id' => $key]);
                $current = $this->tokenStorageInterface->getToken()->getUser();
                $order = new Order();
                $order->setProduct($result[0]->getTitle());
                $order->setPrice($result[0]->getPrice());
                $order->setQuantity($value);
                $order->setUser($current);
                $this->entityManager->Add($order);
                $this->formCartController->remove($key);
            }

            return true;
        }

        return false;
    }

    public function display(User $user): array
    {
        $total = 0;
        $order = $this->orderRepository->findBy(['user' => $user, 'status' => true]);
        foreach ($order as $test) {
            $calcul = $test->getPrice() * $test->getQuantity();
            $total += $calcul;
        }

        return [$order, $total];
    }

    public function deleteAll(User $user): void
    {
        $order = $this->orderRepository->findBy(['user' => $user, 'status' => true]);
        foreach ($order as $delete) {
            $this->entityManager->remove($delete);
        }
    }

    public function accept(User $user): void
    {
        $order = $this->orderRepository->findBy(['user' => $user, 'status' => true]);
        foreach ($order as $delete) {
            $delete->setStatus(false);
            $delete->setDate(new DateTime('now'));
            $this->entityManager->Add($delete);
        }
    }

    public function delete(Order $order): void
    {
        $this->entityManager->remove($order);
    }

    public function addOne(Order $orderEntity): void
    {
        $more = 0;
        $quantity = $orderEntity->getQuantity();
        $more += $quantity + 1;
        $orderEntity->setQuantity($more);
        $this->entityManager->update();
    }

    public function removeOne(Order $orderEntity): void
    {
        if ($orderEntity->getQuantity() > 1) {
            $more = 0;
            $quantity = $orderEntity->getQuantity();
            $more += $quantity - 1;
            $orderEntity->setQuantity($more);
            $this->entityManager->update();
        }
    }
}
