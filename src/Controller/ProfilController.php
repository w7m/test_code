<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 09/01/2019
 * Time: 19:37
 */

namespace App\Controller;

use App\Form\ExpertType;
use App\Form\InsurerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    /**
     * @Route("/expert/profile-edit", name="expert_editProfil")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editExpert(Request $request, EntityManagerInterface $em)
    {
        $expert = $this->getUser();
        $form = $this->createForm(ExpertType::class, $expert);
        $form->add('email', null, array('disabled' => true));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($expert);
            try {
                $em->flush();
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue lors de la modification, veuillez ressayer de nouveau');
                return $this->render('insurance/sinister/admin/updateAgent.html.twig',
                    ['form' => $form->createView()]
                );
            }
            $request->getSession()->getFlashBag()->add('success', 'Vos informations ont été modifié avec succès');
        }
        return $this->render('insurance/expert/profile.html.twig',
            ['form' => $form->createView(), 'expert' => $expert]
        );
    }

    /**
     * @Route("/insurer/profile-edit", name="insurer_editProfil")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editInsurer(Request $request, EntityManagerInterface $em)
    {
        $insurer = $this->getUser();
        $form = $this->createForm(InsurerType::class, $insurer);
        $form->add('email', null, array('disabled' => true));
        $form->add('insurerId', null, array('disabled' => true));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($insurer);
            try {
                $em->flush();
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue lors de la modification, veuillez ressayer de nouveau');
                return $this->render('insurance/sinister/admin/updateAgent.html.twig',
                    ['form' => $form->createView()]
                );
            }
            $request->getSession()->getFlashBag()->add('success', 'Vos informations ont été modifié avec succès');
        }
        return $this->render('insurance/sinister/skeleton_page/profile.html.twig',
            ['form' => $form->createView(), 'insurer' => $insurer]
        );

    }

}