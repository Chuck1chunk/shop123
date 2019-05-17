<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Role;
use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserLoginType;
use App\Form\UserEmailType;
use App\Form\UserResetPasswordType;
use App\Form\UserType;
use function MongoDB\BSON\fromJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("user/signup")
     */
    public function signup(Request $request, Session $session)
    {
        $user = new User();
        $role = new Role();

        /*Form creating*/
        $form = $this->createForm(UserType::class, $user, array('csrf_protection' => false));
        $form->handleRequest($request);

        /*Checking form data for valid */
        if ($form->isSubmitted() && $form->isValid()) {
            //if ok
            $em = $this->getDoctrine()->getManager();
            $userData = $em->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);

            if (!$userData) {

                $user->setName($user->getName());
                $user->setPassword($user->getPassword());
                $user->setEmail($user->getEmail());
                $user->setPostIndex($user->getPostIndex());
                $user->setAddress($user->getAddress());
                $user->setImage('SomeImage');

                $em->persist($user);
                $em->flush();

                $role->setUserId($user->getId());
                $role->setRole('user');

                $em->persist($role);
                $em->flush();

                $session->set('user', $user);

                return $this->render('user/cabinet.html.twig', [
                    'user' => $user
                ]);
            }
            return new Response('Email is already taken');
        }
        return $this->render('formbase.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/login", name="user_login")
     */
    public function login(Request $request, Session $session)
    {
        $user = new User();

        $form = $this->createForm(UserLoginType::class, $user, array('csrf_protection' => false));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([
                'email'    => $user->getEmail(),
                'password' => $user->getPassword(),
            ]);

            if ($user) {

                $session->set('user', $user);

                if (self::checkIsAdmin($user->getId())) {
                    return $this->render('admin/cabinet.html.twig', [
                        'user' => $user,
                    ]);
                } else {
                    return $this->redirectToRoute('user_cabinet');
                }
            }

        }
        return $this->render('user/login.html.twig', [
           'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("user/cabinet", name="user_cabinet")
     */
    public function enterToAcc(Session $session)
    {
        if ($session->get('user')) {
            $userData = $session->get('user');

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([
                'email'    => $userData->getEmail(),
                'password' => $userData->getPassword(),
            ]);
            return $this->render('user/cabinet.html.twig', [
                'user' => $user
            ]);
        } else {
            return new Response('404');
        }
    }

    /**
     * @Route("user/logout", name="user_logout")
     */
    public function logout(Session $session)
    {
        $session->clear();
        unset($session);
        return $this->redirectToRoute('latestproduct');
    }

    /**
     * @param $userId
     * @return bool
     */
    public function checkIsAdmin($userId)
    {
        $role = $this->getDoctrine()->getRepository(Role::class)->findBy([
                'userId' => $userId,
            ]);

        foreach ($role as $roleItem) {

            if ($roleItem->getRole() == 'admin') {
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * @Route("/user/edit/{id}")
     */
    public function update(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setName($user->getName());
            $user->setPassword($user->getPassword());

            $em->flush();

            return $this->redirectToRoute('user_show');
        }
        return $this->render('formbase.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/delete/{id}")
     */
    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("user_show");
    }

}