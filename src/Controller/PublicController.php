<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'app_public')]
    public function publicPage(): Response
    {
        return $this->render('public/index.html.twig', [
            'message' => 'Cette page est accessible à tous.',
        ]);
    }
}