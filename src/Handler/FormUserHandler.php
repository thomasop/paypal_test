<?php

namespace App\Handler;

use App\Entity\User;
use App\Tool\FileUploader;
use App\Tool\EntityManager;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FormUserHandler
{
    /** @var RequestStack */
    private $request;
    /** @var UserPasswordHasherInterface */
    private $passwordEncoder;
    /** @var EntityManager */
    private $entityManager;
    /** @var MailerInterface */
    private $mailer;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, RequestStack $request, EntityManager $entityManager, MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    public function new(FormInterface $form, User $user): bool
    {
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordEncoder->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setToken($this->generateToken());
            $this->entityManager->Add($user);
            return true;
        }
        return false;
    }

    public function forgotPassword(FormInterface $form, User $user): bool
    {
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userRepository->findOneBy(["email" => $email]);
            if ($user) {
                $user->setToken($this->generateToken());

                $this->entityManager->Add($user);
                $message = (new TemplatedEmail())
                    ->from('thomasdasilva010@gmail.com')
                    ->to(htmlspecialchars($form->get('email')->getData()))
                    ->subject('Mot de passe oublié')

                    ->htmlTemplate('user/forgotpassword.html.twig')
                    ->context([
                        'token' => $user->getToken(),
                        'expiration_date' => new \DateTime('+1 days'),
                    ])
                    ;
                $this->mailer->send($message);
                return true;
            }
            return $user;
        }
        return false;
    }

    public function resetPassword(FormInterface $form, User $user): bool
    {
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            if (preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$#', $form->get('password')->getData())) {
                $user->setPassword(
                    $this->passwordEncoder->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $this->entityManager->Add($user);
                return true;
            } elseif (!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$#', $form->get('password')->getData())) {
                return 'erreur';
            }
        }
        return false;
    }

    public function verifyUserEmail(User $userRepository): void
    {
        $userRepository->setEnabled(true);
        $userRepository->setToken(null);
        $this->entityManager->Add($userRepository);
    }

    public function edit(FormInterface $form, User $user): bool
    {
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('pseudo')->getData();
            $user->setPseudo($pseudo);
            $this->entityManager->update();
            return true;
        }
        return false;
    }

    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
