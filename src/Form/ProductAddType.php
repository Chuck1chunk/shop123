<?php


namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
//use App\Controller\ProductController;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                //'choices' => $category->getName()
            ])
            ->add('file', FileType::class//, ['label' => 'Image for product (JPEG, JPG file)']
            )
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
        ));
    }
}