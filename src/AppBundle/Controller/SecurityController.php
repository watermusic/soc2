<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use FOS\UserBundle\Controller\SecurityController as BaseController;


class SecurityController extends BaseController
{

    public function renderLogin(array $data)
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        $data['users'] = array();
        foreach($users as $user) {
            /** @var User $user  */
            array_push($data['users'], $user->getUsernameCanonical());
        }
        sort($data['users']);
        return $this->get('templating')->renderResponse('@App/Security/login.html.twig', $data);
    }

}
