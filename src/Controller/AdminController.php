<?php

namespace App\Controller;

use App\Entity\Expert;
use App\Entity\Insurer;
use App\Entity\User;
use App\Form\ExpertType;
use App\Form\InsurerType;
use App\Form\ParameterType;
use App\Repository\ExpertiseRepository;
use App\Repository\ParameterRepository;
use App\Repository\UserRepository;
use App\Service\AdminService;
use App\Service\FilterListFolder;
use App\Service\Mailer;
use App\Service\PasswordManager;
use DataTables\DataTablesInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home-admin")
     * @param ExpertiseRepository $expertiseRepository
     * @param FilterListFolder $filterListFolder
     * @return Response
     */
    public function index(ExpertiseRepository $expertiseRepository, FilterListFolder $filterListFolder)
    {
        $x = $expertiseRepository->foldersCount();
        $countFolder = $filterListFolder->countFolder($x);
        return $this->render('insurance/sinister/skeleton_page/home.html.twig', ['folderByState' => $countFolder]);
    }

    /**
     * @Route("/experts", name="all-expert")
     */
    public function listExpert()
    {
        return $this->render('insurance/sinister/admin/listExpert.html.twig');
    }

    /**
     * @Route("/handleExperts", name="handle-expert")
     */
    public function handleExpert(Request $request, DataTablesInterface $datatables): JsonResponse
    {
        try {
            $results = $datatables->handle($request, 'experts');
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @Route("/new-experts", name="new-expert")
     * @param Request $request
     * @param AdminService $admin
     * @param PasswordManager $passwordManager
     * @param Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newExpert(Request $request, AdminService $admin, PasswordManager $passwordManager, Mailer $mailer, UserRepository $userRepository)
    {
        $role = User::ROLE_EXPERT;
        $expert = new Expert();
        $form = $this->createForm(ExpertType::class, $expert);
        $formData = $form->getData();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $formData->getEmail();
            $token = bin2hex(random_bytes(User::LENGHT_TOKEN));
            $password = $passwordManager->checkPassword();
            if ($userRepository->findOneByEmail($email) != null) {
                $request->getSession()->getFlashBag()->add('error', 'cette email ' . $email . ' est déja utilisé');
                return $this->render('insurance/sinister/admin/newExpert.html.twig', ['form' => $form->createView()]);
            } else {
                try {
                    $admin->addUser($expert, $password, $token, $role, $email);
                } catch (\Exception $e) {
                    $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de la cration du compte, veuillez réessayer ultérieurement');
                    return $this->render('insurance/sinister/admin/newExpert.html.twig', ['form' => $form->createView()]);
                }
            }
            $body = $this->renderView('Mail/mailCreateAccount.html.twig',
                ['token' => $token, 'password' => $password, 'user' => $expert]);
            try {
                $mailer->sendMail($email, 'Information de compte', $body);
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue lors de l\'envoie de mail');
                return $this->render('insurance/sinister/admin/newExpert.html.twig', ['form' => $form->createView()]);
            }
            $request->getSession()->getFlashBag()->add('success', 'Compte expert a été ajouté avec succès');
            return $this->redirectToRoute('all-expert');
        }
        return $this->render('insurance/sinister/admin/newExpert.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/agents", name="all-repectionist")
     */
    public function listReceptionist()
    {
        return $this->render('insurance/sinister/admin/listReceptionist.html.twig');
    }

    /**
     * @Route("/expertDetails/{id}",name="expertDetails")
     */
    public function expertDetails(Request $request, User $agent, AdminService $adminService)
    {
        if ($agent instanceof Expert) {
            return $this->render('insurance/sinister/admin/expertDetails.html.twig', ['expert' => $agent]);
        } elseif ($agent instanceof Insurer) {
            $role = $adminService->findName($agent->getRoles()[0]);
            return $this->render('insurance/sinister/admin/agentDetails.html.twig', ['agent' => $agent, 'role' => $role]);
        }
    }

    /**
     * @Route("/expertUpdate/{id}",name="expertUpdate")
     */
    public function expertUpdate(Request $request, $id, EntityManagerInterface $em)
    {
        $expert = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(ExpertType::class, $expert);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($expert);
            try {
                $em->flush();
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'ajout, veuillez réessayer ultérieurement');
                return $this->render('insurance/sinister/admin/updateExpert.html.twig',
                    ['form' => $form->createView()]
                );
            }
            $request->getSession()->getFlashBag()->add('success', 'Expert modifié avec succès');
            return $this->render('insurance/sinister/admin/listExpert.html.twig');

        }
        return $this->render('insurance/sinister/admin/updateExpert.html.twig',
            ['form' => $form->createView()]
        );

    }

    /**
     * @Route("/agentUpdate/{id}",name="agentUpdate")
     * @param Request                $request
     * @param                        $id
     * @param EntityManagerInterface $em
     * @param AdminService           $adminService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function agentUpdate(Request $request, $id, EntityManagerInterface $em, AdminService $adminService)
    {
        $agent = $this->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->createForm(InsurerType::class, $agent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($agent);
            try {
                $em->flush();
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'ajout, veuillez réessayer ultérieurement');
                return $this->render('insurance/sinister/admin/updateAgent.html.twig',
                    ['form' => $form->createView()]
                );
            }
            if (in_array(User::ROLE_FINANCIAL, $agent->getRoles())) {
                $request->getSession()->getFlashBag()->add('success', 'Financier modifié avec succès');
                return $this->redirectToRoute('all-financial-service');
            } elseif (in_array(User::ROLE_RECEPTIONIST, $agent->getRoles())) {
                $request->getSession()->getFlashBag()->add('success', 'réceptionniste modifié avec succès');

                return $this->redirectToRoute('all-repectionist');

            } elseif (in_array(User::ROLE_VALIDATOR, $agent->getRoles())) {
                $request->getSession()->getFlashBag()->add('success', 'validateur modifié avec succès');

                return $this->redirectToRoute('all-validation-committee');
            }


        }
        if (in_array(User::ROLE_FINANCIAL, $agent->getRoles())) {
            $role = "financier";
        } elseif (in_array(User::ROLE_RECEPTIONIST, $agent->getRoles())) {
            $role = "receptionnist";
        } elseif (in_array(User::ROLE_VALIDATOR, $agent->getRoles())) {
            $role = "validateur";
        }
        return $this->render('insurance/sinister/admin/updateAgent.html.twig',
            ['form' => $form->createView(), 'role' => $role]
        );

    }

    /**
     * @Route("/new-insurer/{role}", name="new-insurer")
     * @param AdminService $admin
     * @param $role
     * @param Request $request
     * @param Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newInsurer(AdminService $admin, $role, Request $request, Mailer $mailer, UserRepository $userRepository)
    {
        $role = $admin->findRole($role);
        $insurer = new Insurer();
        $form = $this->createForm(InsurerType::class, $insurer);
        $formData = $form->getData();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $matricule = $admin->generateMatricule($role);
            $token = bin2hex(random_bytes(User::LENGHT_TOKEN));
            $email = $formData->getEmail();
            $insurer->setInsurerId($matricule);
            if ($userRepository->findOneByEmail($email) != null) {
                $request->getSession()->getFlashBag()->add('error', 'cette email ' . $email . ' est déja utilisé');
                return $this->render($admin->findViewInsurer($role,'new'), ['form' => $form->createView()]);
            } else {
                try {
                    $admin->addUser($insurer, $token, $role, $email);
                } catch (\Exception $e) {
                    $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'ajout, veuillez réessayer ultérieurement');
                    return $this->render($admin->findViewInsurer($role, 'new'), ['form' => $form->createView()]);
                }
            }
            $body = $this->renderView('Mail/mailCreateAccount.html.twig',
                ['token' => $token, 'user' => $insurer]);
            try {
                $mailer->sendMail($email, 'Information de compte', $body);
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est est survenue lors de l\'envoie de mail');
                return $this->render($admin->findViewInsurer($role, 'new'), ['form' => $form->createView()]);
            }
            $request->getSession()->getFlashBag()->add('success', 'Compte ' . $admin->findName($role) . ' a été ajouté avec succès');
            return $this->redirectToRoute($admin->findViewInsurer($role,'list'));
        }
        return $this->render($admin->findViewInsurer($role, 'new'), ['form' => $form->createView()]);
    }

    /**
     * @Route("/handleReceptionnist", name="handle-receptionnist")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleReceptionnist(Request $request, DataTablesInterface $datatables): JsonResponse
    {

        try {
            // Tell the DataTables service to process the request,
            // specifying ID of the required handler.
            $results = $datatables->handle($request, 'agent');

            return $this->json($results);
        } catch (\Exception $e) {
            // In fact the line below returns 400 HTTP status code.
            // The message contains the error description.
            return $this->json($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @Route("/validation-committee", name="all-validation-committee")
     */
    public function listValidationCommitee()
    {
        return $this->render('insurance/sinister/admin/listValidator.html.twig');
    }

    /**
     * @Route("/handleValidator", name="handle-validator")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleValidator(Request $request, DataTablesInterface $datatables): JsonResponse
    {

        try {
            // Tell the DataTables service to process the request,
            // specifying ID of the required handler.
            $results = $datatables->handle($request, 'validator');
            return $this->json($results);
        } catch (\Exception $e) {
            // In fact the line below returns 400 HTTP status code.
            // The message contains the error description.
            return $this->json($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @Route("/service-financier", name="all-financial-service")
     */
    public function listFinancialService()
    {
        return $this->render('insurance/sinister/admin/listFinancial.html.twig');
    }

    /**
     * @Route("/handleFinancier", name="handle-financier")
     * @param Request $request
     * @param DataTablesInterface $datatables
     * @return JsonResponse
     */
    public function handleFinancier(Request $request, DataTablesInterface $datatables): JsonResponse
    {

        try {
            // Tell the DataTables service to process the request,
            // specifying ID of the required handler.
            $results = $datatables->handle($request, 'financier');

            return $this->json($results);
        } catch (\Exception $e) {
            // In fact the line below returns 400 HTTP status code.
            // The message contains the error description.
            return $this->json($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @Route("/configuration-dossier", name="folder-configuration")
     */
    public function ConfigurationFolder(ParameterRepository $parameterRepository, Session $session)
    {
        return $this->render('insurance/sinister/admin/parameter/show.html.twig', [
            'parameter' => $parameterRepository->findAll()[0],
        ]);
    }

    /**
     * @Route("/configuration-dossier/edit", name="parameter_edit", methods={"GET","POST"})
     * @param Request $request
     * @param ParameterRepository $parameterRepository
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Request $request, ParameterRepository $parameterRepository, ObjectManager $manager): Response
    {
        $parameter = $parameterRepository->findAll()[0];
        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $request->getSession()->getFlashBag()->add('success', 'Les paramètres ont été mis à jour avec succes :)');
            return $this->redirectToRoute('folder-configuration');
        }
        return $this->render('insurance/sinister/admin/parameter/edit.html.twig', [
            'parameter' => $parameter,
            'form' => $form->createView(),
        ]);
    }
}
