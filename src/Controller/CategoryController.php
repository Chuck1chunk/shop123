<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $category = new Category();
        $category->setName('Hoodies');

        $em->persist($category);

        $em->flush();


        return new Response('Saved category with id ' . $category->getId() . ' and name ' . $category->getName());

    }

    /**
     * @Route("/category/{id}", name="category_show")
     */
    public function show(Category $category)
    {
        return new Response('Check out this greate category: '.$category->getName());
    }

    public function getAllCategories()
    {

        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();

        return $categories;
    }

    /**
     * @Route("/category/edit/{id}")
     */
    public function update($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No category fount by id '.$id);
        }

        $category->setName('Jeans');
        $em->flush();

        return $this->redirectToRoute('category_show', [
            'id' => $category->getId()
        ]);
    }

    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Category::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No category found for id '.$id
            );
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('category_show', [
            'category is deleted'
        ]);
    }
}
