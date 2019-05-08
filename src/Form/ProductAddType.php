<?php


namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Controller\ProductController;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Optional;


class ProductAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $category = new Category();

        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('quantitu')

            /*
             * Получить имя категории кинуть его в продуктКонтроллер и там за этим именем найти айди
             * категории и вставить его в таблицу продуктов
             */
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                //'choices' => $category->getName()
            ])
            ->add('save', SubmitType::class)
        ;
    }
}