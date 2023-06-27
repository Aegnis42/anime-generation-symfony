<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        $latestAnime = $this->getDataFromAPI('https://api.jikan.moe/v4/schedules');
        $popularAnime = $this->getDataFromAPI('https://api.jikan.moe/v4/top/anime');
        $animeSuggestions = $this->getDataFromAPI('https://api.jikan.moe/v4/recommendations/anime');
        
        // Sélection aléatoire des suggestions
        shuffle($animeSuggestions);
        
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'titre' => 'Accueil',
            'navNameAccueil' => '',
            'navNameCatalogue' => 'Catalogue',
            'navNameAgenda' => 'Agenda',
            'navNameProfil' => 'Profil',
            'latestAnime' => $latestAnime,
            'popularAnime' => $popularAnime,
            'animeSuggestions' => $animeSuggestions,
        ]);
    }
    
    private function getDataFromAPI(string $url): array
    {
        $client = new Client();
        $response = $client->get($url, [
            'query' => [
                'kids' => 'false',
                'sfw' => 'true',
                'unapproved' => 'false',
            ],
        ]);
        $data = json_decode($response->getBody(), true);
        
        return $data['data'];
    }
}
