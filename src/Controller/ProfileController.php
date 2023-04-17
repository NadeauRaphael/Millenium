<?php

namespace App\Controller;

use App\Entity\Client;
use App\Core\Notification;
use App\Core\NotificationColor;
use App\Form\PasswordFormType;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\UserFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ProfileController extends AbstractController
{
    private $em = null;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->getUser();
        $formUser = $this->createForm(UserFormType::class, $currentUser);
        $formUser->handleRequest($request);
        $formPassword = $this->createForm(PasswordFormType::class);
        $formPassword->handleRequest($request);
        $notification = null;

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $this->em->persist($currentUser);
            $this->em->flush();
            $message = "Profile updated succesfully.";
            $notification = new Notification('Success', $message, NotificationColor::SUCCESS);
        }

        
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {


            $this->em->persist($currentUser);
            $this->em->flush();
            $message = "Profile updated succesfully.";
            $notification = new Notification('Success', $message, NotificationColor::SUCCESS);
        }

        return $this->render('profile/index.html.twig', [
            'currentUser' => $currentUser,
            'UserForm' => $formUser->createView(),
            'PasswordForm' => $formPassword->createView(),
            'notification' => $notification
        ]);
    }

    #[Route('/login', name:'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response 
    {

        if($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $notification = null;
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error != null && $error->getMessageKey() === 'Invalid credentials.') {
            $message = "Wrong combinaison of email and password.";
            $notification = new Notification('error', $message, NotificationColor::WARNING);
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('profile/login.html.twig', [
            'last_username' => $lastUsername,
            'notification' => $notification
        ]);
    }


    #[Route('/logout', name:'app_logout')]
    public function logout() {

        throw new \Exception("Don't forget to activate logout in security.yaml");
    }
}
