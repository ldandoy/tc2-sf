<?php
// src/Controller/WelcomeController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Product;

/**
 * @Route("/")
 */
class WelcomeController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('welcome/index.html.twig', [
            'products' => $products
        ]);
    }
}
