<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;

use App\Form\UserEditType;
use App\Form\UserLoginType;
use App\Form\UserType;

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
use Symfony\Component\Validator\Constraints\Email;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("user/signup")
     */

    public function signup(Request $request)
    {
        $user = new User();
        $role = new Role();

        /*Form making*/
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        /*Checking form data for valid */
        if ($form->isSubmitted() && $form->isValid()) {
            //if ok

            $name    = $user->getName();
            $email   = $user->getEmail();
            $pwd     = $user->getPassword();
            $address = $user->getAddress();
            $index   = $user->getPostIndex();

            $em = $this->getDoctrine()->getManager();
            $res = $em->getRepository(User::class)->findOneBy([
                'email' => $email
            ]);

            if (!$res) {

                $user->setName($name);
                $user->setEmail($email);
                $user->setPassword($pwd);
                $user->setAddress($address);
                $user->setPostIndex($index);
                $user->setImage('SomeImage');

                $em->persist($user);
                $em->flush();


                $role->setUserId($user->getId());
                $role->setRole('user');

                $em->persist($role);
                $em->flush();

                return $this->render('user/cabinet.html.twig', [
                    'id' => $user->getId(),
                    'name' => $name,
                    'password' => $pwd,
                    'email' => $email,
                    'address' => $address,
                    'PostIndex' => $index
                ]);
            }
            return new Response('Email is already taken');
        }
        return $this->render('user/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/login", name="user_login")
     */
    public function login(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserLoginType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name  = $user->getName();
            $email = $user->getEmail();
            $pwd   = $user->getPassword();

            $em = $this->getDoctrine()->getManager();

            $res = $em->getRepository(User::class)->findOneBy([
                'email'    => $email,
                'password' => $pwd,
            ]);


            if ($res) {
                //return new Response('Hello'.' '.$user->getName());
                return $this->render('user/cabinet.html.twig', [
                    'name' => $name,
                ]);
            }
            return new Response('404');
        }

        return $this->render('user/login.html.twig', [
           'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/user/show", name="user_show")
     */
    public function showAllUsers()
    {
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        //->showAllUsers();


        return $this->render('user/usersinfo.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/edit/{id}")
     */
    public function update(Request $request ,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);


        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $user->getName();
            $pwd  = $user->getPassword();

            $user->setName($name);
            $user->setPassword($pwd);

            $em->flush();

            return $this->redirectToRoute('user_show');
        }

        return $this->render('user/edit.html.twig', [
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

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("user_show");
    }

}
