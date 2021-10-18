<?php
// src/Controller/CartController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/cart", name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, SessionInterface $session): Response
    {
        $cartItems = $session->get('cart', []);
        
        $cartItems[] = [
            'productQty' => $request->request->get('productQty'),
            'productId' => $request->request->get('productId')
        ];

        $session->set('cart', $cartItems);

        $this->addFlash(
            'success',
            'Produit bien ajouté à voter panier !'
        );

        return $this->redirectToRoute("products_list");
    }
}