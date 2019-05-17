<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryAddType;
use App\Form\ProductAddType;
use App\Form\ProductEditType;
use App\Service\FileUploader;

use function MongoDB\BSON\fromJSON;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    /**
     * @Route("/", name="latestproduct")
     */
    public function getLatestProducts(Request $request, PaginatorInterface $paginator, Session $session)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)
            ->findAll();

        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('product/latestproducts.html.twig', [
            'categories' => $categories,
            'pagination' => $pagination,
        ]);
    }

    /**
     *@Route("/product/getby/category/{categoryId}")
     */
    public function getProductListByCategory(Request $request, $categoryId, PaginatorInterface $paginator)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)
            ->findBy([
                'categoryId' => $categoryId
            ]);
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );

        return $this->render('product/latestproducts.html.twig', [
            'categories' => $categories,
            'pagination' => $pagination,
        ]);
    }


}
