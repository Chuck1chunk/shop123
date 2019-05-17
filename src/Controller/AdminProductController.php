<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;;

use App\Form\CategoryAddType;
use App\Form\ProductAddType;
use App\Form\ProductEditType;
use App\Service\FileUploader;

use function MongoDB\BSON\fromJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


class AdminProductController extends Controller
{
    /**
     * @Route("/admin/get/productslist", name="product_show")
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
     * @Route("/admin/product/add", name="product_add")
     */
    public function addProduct(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductAddType::class, $product, array('csrf_protection' => false));
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $product->getCategory();

            $image = $product->getFile();
            $imageName = '\uploads\\'.md5(uniqid()).'.'.$image->guessExtension();
            $image->move($this->getParameter('images_directory'), $imageName);

            $productByName = $em->getRepository(Product::class)->findOneBy([
                'name' => $product->getName()
            ]);

            if (!$productByName) {
                $product->setName($product->getName());
                $product->setPrice($product->getPrice());
                $product->setDescription($product->getDescription());
                $product->setQuantitu($product->getQuantitu());
                $product->setCategoryId($category->getId());
                $product->setImage($imageName);

                $em->persist($product);
                $em->flush();
                return $this->redirectToRoute('product_show');
            }
            return new Response('This product is already added');
        }
        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/edit/{id}")
     */
    public function update(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        $form = $this->createForm(ProductAddType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setName($product->getName());
            $product->setPrice($product->getPrice());
            $product->setDescription($product->getDescription());
            $product->setQuantitu($product->getQuantitu());
            $product->setCategoryId($product->getCategory());

            $em->flush();

            return $this->redirectToRoute('product_show');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}")
     */
    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('product_show', [
            'prduct is deleted'
        ]);
    }
}