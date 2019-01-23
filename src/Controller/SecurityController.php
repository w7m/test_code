<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 09/01/2019
 * Time: 19:37
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Service\Mailer;
use App\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/home", name="home-sinister")
     */
    public function homePage()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home-admin');
        } elseif ($this->isGranted('ROLE_VALIDATOR')) {
            return $this->redirectToRoute('home-validator_teams');
        } elseif ($this->isGranted('ROLE_RECEPTIONIST')) {
            return $this->redirectToRoute('home-receptionist');
        } elseif ($this->isGranted('ROLE_FINANCIAL')) {
            return $this->redirectToRoute('home-financial');
        } else {
            return $this->redirectToRoute('app_logout');
        }
    }

    /**
     * @Route("/", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_logout');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/check-activation/{token}", name="check-activation")
     * @param $token
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function checkActivation($token, EntityManagerInterface $em, Request $request)
    {
        $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);
        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $password = password_hash($form->getData()['password'], PASSWORD_BCRYPT);
                $user->setPassword($password);
                $user->setIsActivated(true);
                $user->setToken(null);
                $em->persist($user);
                try {
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('success', 'Votre compte est bien actif');
                    return $this->redirectToRoute('app_login');
                }catch (\Exception $e)
                {
                    $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'activation, 
                veuillez essayé plus tard');
                }
            }
            return $this->render('security/resetPassword.html.twig',['form' => $form->createView()]);
        } else {
            $request->getSession()->getFlashBag()->add('error', 'Le lien d\'activation n\'est pas valide');
            return $this->redirectToRoute('app_login');
        }



    }

    /**
     * @Route("/check-reset/{token}", name="check-reset")
     * @param $token
     * @param PasswordManager $passwordManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param Mailer $mailer
     * @return Response
     */
    public function checkReset($token, EntityManagerInterface $em, Request $request)
    {
        $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);
        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $password = password_hash($form->getData()['password'], PASSWORD_BCRYPT);
                $user->setPassword($password);
                $user->setIsActivated(true);
                $user->setToken(null);
                $em->persist($user);
                try {
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('success', 'Votre mot de passe à été changé avec succès');
                    return $this->redirectToRoute('app_login');
                }catch (\Exception $e)
                {
                    $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue lors de la réinitialisation');
                }
            }
            return $this->render('security/resetPassword.html.twig',['form' => $form->createView()]);
        } else {
            $request->getSession()->getFlashBag()->add('error', 'Le lien de réinitialisation n\'est pas valide');
            return $this->redirectToRoute('app_login');
        }


    }

    /**
     * @Route("/reset", name="app_resetPassword")
     * @param Request $request
     * @param Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function resetPassword(Request $request, Mailer $mailer, EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = bin2hex(random_bytes(User::LENGHT_TOKEN));
            $email = $form->getData()['email'];
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {
                $user->setToken($token);
                $em->persist($user);
                try {
                    $em->flush();
                } catch (\Exception $e) {
                    $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'activation, veuillez essayé plus tard');
                }

                $body = $this->renderView('Mail/mailResetPasswordConfirm.html.twig', [
                    'user' => $user,
                    'token' => $token
                ]);
                try {
                    $mailer->sendMail($email, 'Confirmer la réinitialisation de mot de passe', $body);
                } catch (\Exception $e) {
                    $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'envoie d\'email');
                }
                $request->getSession()->getFlashBag()->add('success', 'Un email de confirmation a été envoyer, Veuillez vérifier votre boite email');
                return $this->redirectToRoute('app_login');
            } else {
                $request->getSession()->getFlashBag()->add('error', 'Email non valide');
            }
        }

        return $this->render('security/forgotPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request)
    {

    }

}
