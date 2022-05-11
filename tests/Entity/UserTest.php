<?php declare(strict_types=1);

namespace App\tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function test(): void
    {
        $user = (new User())
        ->setEmail("test@mail.com")
        ->setPassword("Test1234?")
        ->setPseudo("pseudo");
        self::bootKernel();
        $error = self::getContainer()->get('validator')->validate($user);
        $this->assertCount(0, $error);
    }

    public function testEmail(): void
    {
        $user = new User();
        $mail = "test@mail.com";

        $user->setEmail($mail);
        $this->assertEquals("test@mail.com", $user->getEmail());
    }

    public function testPassword(): void
    {
        $user = new User();
        $password = "Test1234?";

        $user->setPassword($password);
        $this->assertEquals("Test1234?", $user->getPassword());
    }

    public function testpseudo(): void
    {
        $user = new User();
        $pseudo = "test";

        $user->setPseudo($pseudo);
        $this->assertEquals("test", $user->getPseudo());
    }
}