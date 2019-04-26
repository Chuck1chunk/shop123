<?php

namespace App\Controller;

use App\Entity\ProductOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductOrderController extends AbstractController
{
    /**
     * @Route("/order", name="product_order")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $productOrder = new ProductOrder();
        $productOrder->setUserName('Alex');
        $productOrder->setUserPhone('+380871459034');
        $productOrder->setProducts('t-shirt; jeans; coat; sneakers');
        //$productOrder->setDate(2004-05-23, 4:25:10);
        $productOrder->setStatus(1);
        $productOrder->setUserId(7);

        $em->persist();

        $em->flush();

        return new Response('Saved new order with id '.$productOrder->getId());
    }
}
