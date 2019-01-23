<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 08/01/2019
 * Time: 11:20
 */

namespace App\Service;

use App\Entity\Expert;
use App\Entity\Insurer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AdminService
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $role
     * @return string
     */
    public function findRole($role)
    {
        switch ($role) {
            case 'financial':
                return User::ROLE_FINANCIAL;
                break;
            case 'receptionist':
                return User::ROLE_RECEPTIONIST;
                break;
            case 'validator':
                return User::ROLE_VALIDATOR;
                break;
        }
    }

    /**
     * @param $role
     * @return string
     */
    public function findViewInsurer($role, $type)
    {
        switch ($role) {
            case User::ROLE_FINANCIAL:
                if ($type == 'new'){
                    return 'insurance/sinister/admin/newFinancial.html.twig';
                }elseif ($type == 'list')
                {
                    return 'all-financial-service';
                }
                break;
            case User::ROLE_RECEPTIONIST:
                if ($type == 'new'){
                    return 'insurance/sinister/admin/newReceptionist.html.twig';
                }elseif ($type == 'list')
                {
                    return 'all-repectionist';
                }
                break;
            case User::ROLE_VALIDATOR:
                if ($type == 'new'){
                    return 'insurance/sinister/admin/newValidator.html.twig';
                }elseif ($type == 'list')
                {
                    return 'all-validation-committee';
                }
                break;
        }
    }

    /**
     * @param $role
     * @return string
     */
    public function findName($role)
    {
        switch ($role) {
            case User::ROLE_FINANCIAL:
                return 'financier';
                break;
            case User::ROLE_RECEPTIONIST:
                return 'receptionniste';
                break;
            case User::ROLE_VALIDATOR:
                return 'validateur';
                break;
        }
    }

    /**
     * @param $user
     * @param $password
     * @param $token
     */
    public function addUser($user, $token, $role, $email)
    {
        $user->setUsername($email);
        $user->setPassword('');
        $user->setIsActivated(false);
        $user->setToken($token);
        $user->setRoles([$role]);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function generateMatricule($role)
    {
        $res = $this->em->getRepository(User::class)->findOneBy([],['id' => 'DESC']);
        if ($res) {
            $id = $res->getId() + 1;
        }else{
            $id = uniqid();
        }
        switch ($role) {
            case User::ROLE_FINANCIAL:
                return 'fin_'.$id;
                break;
            case User::ROLE_RECEPTIONIST:
                return 'rec_'.$id;
                break;
            case User::ROLE_VALIDATOR:
                return  'val_'.$id;
                break;
        }

    }

}