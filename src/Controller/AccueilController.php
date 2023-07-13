<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

// AccueilController est un contrôleur qui hérite de AbstractController
class AccueilController extends AbstractController

{
    // Définit la route de la méthode index
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        // Crée une nouvelle instance de Client Guzzle pour faire des requêtes HTTP
        $client = new Client();

        // Fait une requête POST à l'API AniList avec un en-tête et un corps spécifiques
        $response = $client->request('POST', 'https://anilist-graphql.p.rapidapi.com/', [
            'headers' => [
                'X-RapidAPI-Host' => 'anilist-graphql.p.rapidapi.com',
                'X-RapidAPI-Key' => '6c0ccbfc24mshe8f5e23bcd6c445p173993jsne63638fc4bfd',
                'content-type' => 'application/json',
            ],
            'json' => [
                'query' => '{
                    releasing: Page(page: 1, perPage: 25) {
                        media(type: ANIME, status: RELEASING, isAdult: false, sort: START_DATE_DESC) {
                            id
                            title {
                                romaji
                                english
                                native
                            }
                            startDate {
                                year
                                month
                                day
                            }
                            format
                            status
                            episodes
                            duration
                            genres
                            averageScore
                            popularity
                            coverImage {
                                large
                            }
                            bannerImage
                            description
                            siteUrl
                        }
                    },
                    popular: Page(page: 1, perPage: 25) {
                        media(type: ANIME, isAdult: false, sort: POPULARITY_DESC) {
                            id
                            title {
                                romaji
                                english
                                native
                            }
                            startDate {
                                year
                                month
                                day
                            }
                            format
                            status
                            episodes
                            duration
                            genres
                            averageScore
                            popularity
                            coverImage {
                                large
                            }
                            bannerImage
                            description
                            siteUrl
                        }
                    },
                    random: Page(page: 1, perPage: 100) {
                        media(type: ANIME, isAdult: false) {
                            id
                            title {
                                romaji
                                english
                                native
                            }
                            startDate {
                                year
                                month
                                day
                            }
                            format
                            status
                            episodes
                            duration
                            genres
                            averageScore
                            popularity
                            coverImage {
                                large
                            }
                            bannerImage
                            description
                            siteUrl
                        }
                    }
                }'
            ]
        ]);

        // Décode la réponse JSON en tableau associatif PHP
        $data = json_decode($response->getBody(), true);
    
        // Mélange les données récupérées
        shuffle($data['data']['random']['media']);
    
        // Ne prend que les 25 premiers éléments après mélange
        $data_random = array_slice($data['data']['random']['media'], 0, 25);

        // Renvoie une vue en passant les données récupérées à la vue
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'titre' => 'Accueil',
            'navNameAccueil' => '',
            'navNameCatalogue' => 'Catalogue',
            'navNameAgenda' => 'Agenda',
            'navNameProfil' => 'Profil',
            'animes' => $data['data']['releasing']['media'],
            'popular_animes' => $data['data']['popular']['media'],
            'random_animes' => $data_random
        ]);
    }
}
