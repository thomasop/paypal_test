<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetType;
use App\Tool\EntityManager;
use App\Form\UserPseudoType;
use App\Form\UserSearchType;
use App\Form\RegistrationType;
use App\Handler\FormUserHandler;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    /** @var UserRepository */
    private $userRepository;
    /** @var FormUserHandler */
    private $formUserHandler;
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(UserRepository $userRepository, FormUserHandler $formUserHandler, TokenStorageInterface $tokenStorage)
    {
        $this->userRepository = $userRepository;
        $this->formUserHandler = $formUserHandler;
        $this->tokenStorage = $tokenStorage;
    }

    #[route('/user/display', name: 'user_index')]
    #[IsGranted('ROLE_ADMIN', statusCode: 404, message: 'Vous n\'avez pas acces a cette page!')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $users = null;
        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('pseudo')->getData();
            $users = $userRepository->search($pseudo);
            if ($users != null) {
                $this->addFlash(
                    'success',
                    'Utilisateur trouvé!'
                );
            } else {
                $this->addFlash(
                    'success',
                    'Aucun utilisateur trouvé!'
                );
            }
            return $this->render('user/index.html.twig', [
                'users' => $users,
                'form' => $form->createView()
            ]);
        }
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ["GET", "POST"])]
    public function new(): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        if ($this->formUserHandler->new($form, $user) === true) {
            $this->addFlash(
                'success',
                'Compte créé, veuillez verifier votre email pour activer votre compte !'
            );
            return $this->redirectToRoute('app_login');
        }
        return $this->render('form/formuser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail($token): Response
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);
        $this->formUserHandler->verifyUserEmail($user);
        $this->addFlash(
            'success',
            'Compte valide, connectez vous !'
        );
        return $this->redirectToRoute('app_login');
    }

    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class);
        if ($this->formUserHandler->forgotPassword($form, $user) == true) {
            $this->addFlash(
                'success',
                'Adresse mail trouvé, veuillez verifier votre email pour changer votre mot de passe!'
            );
            return $this->redirectToRoute('app_login');
        }
        if (empty($this->formUserHandler->forgotPassword($form, $user))) {
            $this->addFlash(
                'success',
                'Adresse mail pas trouvé, rentrez une adresse mail valide!!'
            );
            return $this->redirectToRoute('forgot_password');
        }
        return $this->render('form/formforgotpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function resetPassword($token): Response
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);
        if ($user) {
            $form = $this->createForm(ResetType::class);
            if ($this->formUserHandler->resetPassword($form, $user) == true) {
                $this->addFlash(
                    'success',
                    'Mot de passe modifié !'
                );
                return $this->redirectToRoute('app_login');
            } elseif ($this->formUserHandler->resetPassword($form, $user) == 'erreur') {
                $this->addFlash(
                    'success',
                    'Mot de passe incorrect: Une lettre en majuscule, minuscule, un chiffre et caractère speciaux attendu ainsi que 8 caractères minimum!'
                );
            }
        } else {
            $this->addFlash(
                'success',
                'Vous n\'avez pas accès à cette page!'
            );
            return $this->redirectToRoute('app_login');
        }
        return $this->render('form/formresetpassword.html.twig', [
                'form' => $form->createView()
            ]);
    }

    #[route('/user/profile/{id}', name: 'profile')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas acces a cette page!')]
    public function show(User $user): Response
    {
        if ($user)
        {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            
            ]);
        }
        $this->addFlash(
            'success',
            'Vous n\'avez pas accès à cette page!'
        );
        return $this->redirectToRoute('product');
    }

    #[route('/modification/user/{id}', name: 'update_user')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas acces a cette page!')]
    public function edit(User $user): Response
    {
        if($user)
        {
            $form = $this->createForm(UserPseudoType::class, $user);
            if($this->formUserHandler->edit($form, $user) === true)
            {
                $this->addFlash(
                    'success',
                    'Pseudo modifié!'
                );
                return $this->redirectToRoute('profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        $this->addFlash(
            'success',
            'Vous n\'avez pas accès à cette page!'
        );
        return $this->redirectToRoute('product');
    }

    #[route('/user/delete/{id}', name: 'user_delete')]
    #[IsGranted('ROLE_USER', statusCode: 404, message: 'Vous n\'avez pas acces a cette page!')]
    public function delete(Request $request, User $user, EntityManager $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
        }
        $request->getSession()->invalidate();
        $this->tokenStorage->setToken();
        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }

    #[route('/user/admin/delete/{id}', name: 'admin_delete')]
    #[IsGranted('ROLE_ADMIN', statusCode: 404, message: 'Vous n\'avez pas acces a cette page!')]
    public function deleteUser(Request $request, User $user, EntityManager $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
        }
        $this->addFlash('success', 'Utilisateur ' . $user->getPseudo() . ' supprimé');
        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
