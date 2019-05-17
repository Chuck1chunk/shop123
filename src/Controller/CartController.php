<?php


namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use function MongoDB\BSON\fromJSON;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @Route("/cart/add/{productId}")
     */
    public function add(Request $request, Session $session, $productId)
    {
        $cart = new Cart();
        $em = $this->getDoctrine()->getManager();


        $cart->setProductId($productId);

        if (!$productId) {
            return $this->redirectToRoute('/');
        } else {
            $user = $session->get('user');
            $cart->setUserId($user->getId());
        }

        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute("latestproduct");
    }

    /**
     * @Route("/cart/show/{userId}", name="cart_show")
     */
    public function show($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository(Cart::class)->findBy([
            'userId' => $userId
        ]);

        foreach ($cart as $cartItem)
        {
            $productId = $cartItem->getProductId();
            $products = $em->getRepository(Product::class)->findBy([
                'id' => $productId
            ]);
            foreach ($products as $product)
                $productsArray[] = $product;
        }

        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
            'products' => $productsArray,
        ]);
    }

    /**
     * @Route("/cart/remove/{id}")
     */
    public function remove($id, Session $session)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository(Cart::class)->find($id);
        $user = $session->get('user');

        $em->remove($cart);
        $em->flush();

        return $this->redirectToRoute("cart_show", [
            'userId' => $user->getId(),
        ]);
    }

    /**
     * @Route("cart/count")
     */
    public function countItems(Session $session)
    {
        $user = $session->get('user');
        $userId = $user->getId();

        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository(Cart::class)->findBy([
            'userId' => $userId
        ]);

        if ($cart) {
            $count = 0;
            while ($count < count($cart)) $count++;

            return $this->render('base.html.twig', [
               'productCount' => $count
            ]);
//            return $count;
        } else {
            return $this->render('base.html.twig');
            //return 0;
        }
    }
}