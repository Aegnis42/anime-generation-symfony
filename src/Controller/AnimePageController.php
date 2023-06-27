<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimePageController extends AbstractController
{
    #[Route('/anime_page', name: 'app_anime_page')]
    public function index(): Response
    {
        return $this->render('anime_page/index.html.twig', [
            'controller_name' => 'AnimePageController',
        ]);
    }
}
