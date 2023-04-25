<?php

namespace App\Controller;

use App\Entity\Client;
use App\Core\Notification;
use App\Core\NotificationColor;
use App\Entity\Order;
use App\Form\PasswordFormType;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\UserFormType;
use SebastianBergmann\Environment\Console;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->getUser();
        $formUser = $this->createForm(UserFormType::class, $currentUser);
        $formUser->handleRequest($request);
        $formPassword = $this->createForm(PasswordFormType::class);
        $formPassword->handleRequest($request);
        $notification = null;

        // Form User
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $this->em->persist($currentUser);
            $this->em->flush();
            $this->addFlash('updateProfile', 
            new Notification('Success', 'Profile updated succesfully.', NotificationColor::SUCCESS));
        }

        // Form password
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $oldPassword= $formPassword->get('oldPassword')->getData();
            $newPassword= $formPassword->get('password')->getData();
            if ($passwordHasher->isPasswordValid($currentUser, $oldPassword)) {
                if($oldPassword == $newPassword){
                    $this->addFlash('error', 
                    new Notification('error', "The new password can't be the same as the current password", NotificationColor::DANGER));
                }
                else{
                    $currentUser->setPassword(
                        $passwordHasher->hashPassword(
                            $currentUser,
                            $formPassword->get('password')->getData()
                        )
                    );
                    $this->em->persist($currentUser);
                    $this->em->flush();
                    $this->addFlash('updatePassword', 
                    new Notification('Success', 'Password updated succesfully.', NotificationColor::SUCCESS));
                }
            }else{
                $this->addFlash('error', 
                new Notification('error', "The password doesn't match the current password.", NotificationColor::DANGER));
            }
        }

        
        return $this->render('profile/index.html.twig', [
            'currentUser' => $currentUser,
            'UserForm' => $formUser->createView(),
            'PasswordForm' => $formPassword->createView(),
            'notification' => $notification
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $notification = null;
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error != null && $error->getMessageKey() === 'Invalid credentials.') {
            $message = "Wrong combinaison of email and password.";
            $notification = new Notification('error', $message, NotificationColor::WARNING);
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('profile/login.html.twig', [
            'last_username' => $lastUsername,
            'notification' => $notification
        ]);
    }
    #[Route('/orders', name: 'app_orders')]
    public function orders()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->getUser();

        return $this->render('profile/orders.html.twig', [
            "currentUser" => $currentUser
        ]);
    }
    #[Route('/order/{idOrder}', name: 'app_order')]
    public function order($idOrder,Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $Order = $this->em->getRepository(Order::class)->find($idOrder);
        
        return $this->render('profile/order.html.twig', [
            "order" => $Order
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception("Don't forget to activate logout in security.yaml");
    }
}
