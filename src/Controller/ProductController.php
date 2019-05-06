<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductAddType;
use App\Form\ProductEditType;


use function MongoDB\BSON\fromJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;;

use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{

    /**
     * @Route("/product/show", name="product_show")
     */
    public function showAllProducts()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)
                        ->findAll();

        return $this->render('product/productslist.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/add", name="product_add")
     */
    public function add(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductAddType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $product->getName();
            $price  = $product->getPrice();
            $description = $product->getDescription();
            $quantity = $product->getQuantitu();

            $em = $this->getDoctrine()->getManager();
            $res = $em->getRepository(Product::class)->findOneBy([
                'name' => $name
            ]);

            if (!$res) {
                $product->setName($name);
                $product->setPrice($price);
                $product->setDescription($description);
                $product->setQuantitu($quantity);

                $em->persist($product);
                $em->flush();
                //
                return $this->redirectToRoute('product_show');
            }
            return new Response('This product is already added');
        }
        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/edit/{id}")
     */
    public function update(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);


        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $product->getName();
            $price  = $product->getPrice();
            $description = $product->getDescription();
            $quantity = $product->getQuantitu();


            $product->setName($name);
            $product->setPrice($price);
            $product->setDescription($description);
            $product->setQuantitu($quantity);

            $em->flush();

            return $this->redirectToRoute('product_show');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/delete/{id}")
     */
    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('product_show', [
            'prduct is deleted'
        ]);
    }


}
