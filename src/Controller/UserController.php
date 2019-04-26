<?php

namespace App\Controller;

use App\Entity\User;

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

    public function sigup(Request $request)
    {
        $user = new User();

        /*Form making*/
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        /*Checking form data for valid */
        if ($form->isSubmitted() && $form->isValid()) {
            //if ok

            $name  = $user->getName();
            $email = $user->getEmail();
            $pwd   = $user->getPassword();

            $em = $this->getDoctrine()->getManager();

            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($pwd);
            $user->setRole('user');

            $em->persist($user);
            $em->flush();

            //return new Response('Success');
            return $this->redirectToRoute('user_login');

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
            } else {
                return new Response('404');
            }

        }

        return $this->render('user/login.html.twig', [
           'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/edit/{id}")
     */
    public function update($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);


        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $user->setName('Alex');
        $em->flush();

        return $this->redirectToRoute('user_show', [
            'id' => $user->getId(),
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

        return $this->redirectToRoute('user_show', [
            'user is deleted'
        ]);
    }



}
