<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProductTest extends KernelTestCase
{
    public function test(): void
    {
        $product = (new Product())
        ->setPrice(10)
        ->setTitle('title')
        ->setImage('image.jpg');
        self::bootKernel();
        $error = self::getContainer()->get('validator')->validate($product);
        $this->assertCount(0, $error);
    }

    public function testPrice(): void
    {
        $product = new Product();
        $price = 10;

        $product->setPrice($price);
        $this->assertEquals(10, $product->getPrice());
    }

    public function testTitle(): void
    {
        $product = new Product();
        $title = 'title';

        $product->setTitle($title);
        $this->assertEquals('title', $product->getTitle());
    }

    public function testImage(): void
    {
        $product = new Product();
        $image = 'image.jpg';

        $product->setImage($image);
        $this->assertEquals('image.jpg', $product->getImage());
    }
}
