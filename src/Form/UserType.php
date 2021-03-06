<?php


namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('image', IMAGETYPE_JPEG)
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Enter your name'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Enter your email'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Enter your password'
                ]
            ])
            ->add('address', null, [
                'attr' => [
                    'placeholder' => 'Enter your address'
                ]
            ])
            ->add('PostIndex', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Enter your post index'
                ]
            ])
            ->add('save', SubmitType::class)
        ;
    }
}