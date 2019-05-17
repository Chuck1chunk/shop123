<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CategoryAddType;
use App\Form\ProductAddType;
use App\Form\ProductEditType;
use App\Form\UserEmailType;
use App\Form\UserResetPasswordType;
use App\Service\FileUploader;

use function MongoDB\BSON\fromJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


class AdminNotificationController extends Controller
{
    /**
     * @Route("/admin/notification/sale", name="admin_send_mail")
     */
    public function sendSaleEmail(Request $request, \Swift_Mailer $mailer)
    {
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();

        if (isset($_POST['submit']))
        {
            foreach ($users as $user)
            {
                $message = (new \Swift_Message('Sale'))
                    //->setFrom('grisha.franch@gmail.com')
                    ->setFrom($_POST['from'])
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            $_POST['file'],
                            array('name' => $user->getName())
                        ),
                        'text/html'
                    );

                $mailer->send($message);
            }
            return $this->redirectToRoute('latestproduct');
        }
        return $this->render('admin/createmail.html.twig');
    }

    /**
     * @Route("/admin/notification/resetpassword")
     */
    public function sendResetPasswordMail(Request $request, \Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(UserEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $user->getEmail();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([
                'email' => $email
            ]);

            $id = $user->getId();

            $message = (new \Swift_Message('Reset Password'))
                ->setFrom('grisha.franch@gmail.com')
                ->setTo($email)
                ->setBody(
                    '<a href="http://localhost:8000/admin/changepassword/'.$id.'">Reset password</a>',
                    'text/html'
                );

            $mailer->send($message);
            return new Response("success");
        }

        return $this->render('user/email.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/changepassword/{id}", name="user_changePwd")
     */
    public function changePassword(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $pwd = $user->getPassword();
            $user->setPassword($pwd);

            $em->flush();

            return $this->render('user/cabinet.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('user/resetpassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}