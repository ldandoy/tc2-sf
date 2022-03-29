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
use Psr\Log\LoggerInterface;

use App\Entity\Product;
use App\Entity\Order;
use App\Entity\User;

/**
 * @Route("/paiement", name="paiement_")
 */
class PaiementController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @IsGranted("ROLE_USER")
     */
    public function index(EntityManagerInterface $em, Request $request, SessionInterface $session): Response
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
        
        $cartItems = $session->get('cart', []);
        // dd($cartItems);

        $cart = [];
        foreach($cartItems as $item) {
            $cart[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product']->getName(),
                    ],
                    'unit_amount' => $item['product']->getPrice()*100,
                    'tax_behavior' => 'exclusive',
                ],
                // 'tax_rates' => 2000,
                'quantity' => $item['productQty'],
            ];
        }

        // dd($cart);
        
        $strpie_session = \Stripe\Checkout\Session::create([
            'line_items' => $cart,
            // 'customer_email' => $user = $this->getUser()->getEmail(),
            'customer' => \Stripe\Customer::create(array(
                'email' => $this->getUser()->getEmail(),
                'address' => array(
                    'line1' => "test",
                    'city' => "Paris"
                ),    
                'shipping' => [
                    'name' => 'test',
                    'address' => [
                        'city' => 'Paris'
                    ]
                ],
            )),
            'customer_update' => array(
                'shipping' => 'auto'
            ),
            'submit_type' => 'pay',
            'phone_number_collection' => [
                'enabled' => true
            ],
            'shipping_address_collection' => [
                'allowed_countries' => ['FR'],
            ],
            'payment_intent_data' => [
                'setup_future_usage' => 'off_session',
            ],
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'automatic_tax' => [
                'enabled' => true,
            ],
            'success_url' => $this->generateUrl('paiement_success', [], UrlGeneratorInterface::ABSOLUTE_URL)."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $this->generateUrl('paiement_canceled', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        
        // dd($session);

        return $this->redirect($strpie_session->url, 303);
    }

    /**
     * @Route("/success", name="success")
     * @IsGranted("ROLE_USER")
     */
    public function success(Request $request, SessionInterface $session, NotifierInterface $notifier): Response
    {
        // dd($request);

        $this->addFlash(
            'success',
            'Paiement effectué !'
        );

        $user = $this->getUser();
        
        if ($user && $user->getPhonenumber() != ""  && $user->getEmail() != "") {
            // $notification = (new Notification('Paiement effectué', ['email', 'sms']))
            $notification = (new Notification('Paiement effectué', ['sms']))
                ->content('Vous avez avez effectué un paiement !');

            $recipient = new Recipient(
                $user->getEmail(),
                $user->getPhonenumber()
            );

            // $notifier->send($notification, $recipient);
        }

        // $session->set('cart', []);
        
        return $this->render('paiement/success.html.twig', []);
    }

    /**
     * @Route("/canceled", name="canceled")
     * @IsGranted("ROLE_USER")
     */
    public function canceled(Request $request, SessionInterface $session, NotifierInterface $notifier): Response
    {
        $this->addFlash(
            'danger',
            'Paiement annulé !'
        );

        $user = $this->getUser();
        
        if ($user && $user->getPhonenumber() != ""  && $user->getEmail() != "") {
            // $notification = (new Notification('Paiement effectué', ['email', 'sms']))
            $notification = (new Notification('Paiement annulé', ['sms']))
                ->content('Votre paiement a été annulé !');

            $recipient = new Recipient(
                $user->getEmail(),
                $user->getPhonenumber()
            );

            // $notifier->send($notification, $recipient);
        }
        
        return $this->redirectToRoute("products_list");
    }

    /**
     * @Route("/webhook", name="webhook")
     */
    public function webhook (Request $request, LoggerInterface $logger, EntityManagerInterface $em, NotifierInterface $notifier):Response
    {
        $endpoint_secret = $_ENV['WEBHOOK_SECRET'];
        
        $logger->info("Passer");

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            $logger->info($event);
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }
        
        switch ($event->type) {
            case 'payment_intent.created':
                $intent = $event->data->object;
                $logger->info("payment_intent.created...");
                $logger->info($intent);
                $order = new Order();
                $order->setStripeId($intent->client_secret);
                $order->setPaymentIntent($intent->id);
                $order->setAmountSubtotal($intent->amount);
                $order->setCurrency($intent->currency);
                $order->setStatus('Created');
                $em->persist($order);
                $em->flush();
                break;
            case 'checkout.session.completed':
                $session = $event->data->object;
                $logger->info("checkout.session.completed...");
                $logger->info($session);

                // Create the order
                // link user => customer_details => email
                $order = $em->getRepository(Order::class)->findOneBy(['payment_intent' => $session->payment_intent]);
                $order->setAmountTotal($session->amount_total);
                $order->setAmountTax($session->total_details->amount_tax);
                $order->setStatus('Processing');

                $user = $em->getRepository(User::class)->findOneBy(['email' => $session->customer_email]);
                if ($user !== NULL) {
                    $order->setUser($user);
                }

                if ($session->payment_status == 'paid') {
                    // Update order => Paid
                    $order->setStatus($session->payment_status);
                }

                
                $em->persist($order);
                $em->flush();
                break;
            case 'payment_intent.payment_failed':
                $session = $event->data->object;
                $logger->info("payment_intent.payment_failed...");
                $logger->info($session);

                $order = $em->getRepository(Order::class)->findOneBy(['payment_intent' => $session->id]);
                $order->setStatus('Failed');
                $em->persist($order);
                $em->flush();
                break;
        }

        return new Response('ok', Response::HTTP_OK);
    }
}