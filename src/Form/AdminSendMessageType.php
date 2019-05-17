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

class AdminSendMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from')
            ->add('file', FileType::class//, ['label' => 'Image for product (JPEG, JPG file)']
            )
            ->add('save', SubmitType::class)
        ;
    }
}