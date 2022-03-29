<?php
// src/Controller/CartController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Entity\Product;

/**
 * @Route("/cart", name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function add(EntityManagerInterface $em, Request $request, SessionInterface $session, NotifierInterface $notifier): Response
    {
        $cartItems = $session->get('cart', []);
        
        $found = false;
        foreach($cartItems as $key => $item) {
            if ($item['product']->getId() == $request->request->get('productId')) {
                $item['productQty'] = $item['productQty'] + $request->request->get('productQty');
                $found = true;
                $cartItems[$key] = $item;
            }
        }

        if (!$found) {
            $cartItems[] = [
                'productQty'    => $request->request->get('productQty'),
                'product'       => $em->getRepository(Product::class)->find($request->request->get('productId'))
            ];
        }

        $session->set('cart', $cartItems);

        $this->addFlash(
            'success',
            'Produit bien ajouté à voter panier !'
        );

        // The receiver of the Notification
        $user = $this->getUser();
        
        if ($user && $user->getPhonenumber() != ""  && $user->getEmail() != "") {
            $notification = (new Notification('New product in cart', ['sms']))
                ->content('Vous avez ajoutez un nouveau produit au panier');

            $recipient = new Recipient(
                $user->getEmail(),
                $user->getPhonenumber()
            );

            // $notifier->send($notification, $recipient);
        }
        return $this->redirectToRoute("products_list");
    }

    /**
     * @Route("/", name="show")
     */
    public function show(Request $request, SessionInterface $session): Response
    {
        // $session->set('cart', []);

        return $this->render('cart/show.html.twig', [
            'cartItems' => $session->get('cart', [])
        ]);
    }
}