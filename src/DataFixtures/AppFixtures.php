<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordHasherInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 31; ++$i) {
            $product = new Product();
            $product->setPrice(mt_rand(5, 50));
            $product->setTitle('Titre produit'.$i);
            $product->setImage('image'.$i.'.jpeg');
            $manager->persist($product);
        }
        $user = new User();
        $user->setPseudo('test');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail('admin@mail.com');
        $user->setEnabled(true);
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'Test1234?'));
        $manager->persist($user);
        for ($i = 1; $i < 31; ++$i) {
            $order = new Order();
            $order->setPrice($i);
            $order->setProduct('Titre produit'.$i);
            $order->setQuantity(2);
            $order->setStatus(true);
            $order->setUser($user);
            $manager->persist($order);
        }
        $manager->flush();
    }
}
