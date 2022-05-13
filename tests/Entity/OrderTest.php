<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Order;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderTest extends KernelTestCase
{
    public function test(): void
    {
        $order = (new Order())
        ->setProduct('Test')
        ->setPrice(10)
        ->setQuantity(2)
        ->setDate(new DateTime('now'))
        ->setStatus(true);
        self::bootKernel();
        $error = self::getContainer()->get('validator')->validate($order);
        $this->assertCount(0, $error);
    }

    public function testProduct(): void
    {
        $order = new Order();
        $product = 'test';

        $order->setProduct($product);
        $this->assertEquals('test', $order->getProduct());
    }

    public function testQuantity(): void
    {
        $order = new Order();
        $quantity = 1;

        $order->setQuantity($quantity);
        $this->assertEquals(1, $order->getQuantity());
    }

    public function testDate(): void
    {
        $order = new Order();
        $date = new DateTime('2011-01-01T15:03:01.012345Z');

        $order->setDate($date);
        $this->assertEquals(new DateTime('2011-01-01T15:03:01.012345Z'), $order->getDate());
    }

    public function testStatus(): void
    {
        $order = new Order();
        $status = true;

        $order->setStatus($status);
        $this->assertEquals(true, $order->getStatus());
    }

    public function testPrice(): void
    {
        $order = new Order();
        $price = 10;

        $order->setPrice($price);
        $this->assertEquals(10, $order->getPrice());
    }
}
