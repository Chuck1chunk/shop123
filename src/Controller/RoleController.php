<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;


use App\Form\UserRoleType;

use function MongoDB\BSON\fromJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class RoleController extends Controller
{

    /**
     * @Route("/roles/show", name="roles_show")
     */
    public function userRoleShow()
    {
        $roles = $this->getDoctrine()->getRepository(Role::class)
            ->findAll();

        return $this->render('user/showroles.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/roles/edit/{id}")
     */
    public function updateRole(Request $request ,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $role = $em->getRepository(Role::class)->find($id);


        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userId = $role->getUserId();
            $roleName = $role->getRole();

            $role->setUserId($userId);
            $role->setRole($roleName);

            $em->flush();

            return $this->redirectToRoute('roles_show');
        }

        return $this->render('user/editroles.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("roles/add", name="roles_add")
     */
    public function addUserRole(Request $request) {
        $role = new Role();

        /*Form making*/
        $form = $this->createForm(UserRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $role->getUserId();
            $roleName = $role->getRole();

            $em = $this->getDoctrine()->getManager();

            $role->setRole($roleName);
            $role->setUserId($userId);

            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute('roles_show');
        }
        return $this->render('user/editroles.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
