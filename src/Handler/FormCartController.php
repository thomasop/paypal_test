<?php

namespace App\Handler;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FormCartController
{
    /** @var ProductRepository */
    private $productRepository;
    /** @var SessionInterface */
    private $session;

    public function __construct(ProductRepository $productRepository, SessionInterface $session)
    {
        $this->productRepository = $productRepository;
        $this->session = $session;
    }
    
    public function index(): array
    {
        $panier = $this->session->get('panier', []);
        $panierdata = [];
        foreach ($panier as $id => $quantity) {
            $panierdata[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        $total = 0;
        foreach ($panierdata as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return [$panierdata, $total];
    }

    public function add($id): array
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
        return $panier;
    }

    public function remove($id): void
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function removeOne($id): void
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } 
        } else {
            $panier[$id] = 1;
        }
        $this->session->set('panier', $panier);
    }
}