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
        $client = new Client();

        $response = $client->request('POST', 'https://anilist-graphql.p.rapidapi.com/', [
            'headers' => [
                'X-RapidAPI-Host' => 'anilist-graphql.p.rapidapi.com',
                'X-RapidAPI-Key' => '6c0ccbfc24mshe8f5e23bcd6c445p173993jsne63638fc4bfd',
                'content-type' => 'application/json',
            ],
            'json' => [
                'query' => '{
                    Page(page: 1, perPage: 25) {
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
                    }
                }'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        $response_popular = $client->request('POST', 'https://anilist-graphql.p.rapidapi.com/', [
            'headers' => [
                'X-RapidAPI-Host' => 'anilist-graphql.p.rapidapi.com',
                'X-RapidAPI-Key' => '6c0ccbfc24mshe8f5e23bcd6c445p173993jsne63638fc4bfd',
                'content-type' => 'application/json',
            ],
            'json' => [
                'query' => '{
                    Page(page: 1, perPage: 25) {
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
                    }
                }'
            ]
        ]);

        $data_popular = json_decode($response_popular->getBody(), true);

        $response_random = $client->request('POST', 'https://anilist-graphql.p.rapidapi.com/', [
            'headers' => [
                'X-RapidAPI-Host' => 'anilist-graphql.p.rapidapi.com',
                'X-RapidAPI-Key' => '6c0ccbfc24mshe8f5e23bcd6c445p173993jsne63638fc4bfd',
                'content-type' => 'application/json',
            ],
            'json' => [
                'query' => '{
                    Page(page: 1, perPage: 100) {
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

        $data_random = json_decode($response_random->getBody(), true);

        // Mélange les données récupérées
        shuffle($data_random['data']['Page']['media']);

        // Ne prend que les 25 premiers éléments après mélange
        $data_random = array_slice($data_random['data']['Page']['media'], 0, 25);

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'titre' => 'Accueil',
            'navNameAccueil' => '',
            'navNameCatalogue' => 'Catalogue',
            'navNameAgenda' => 'Agenda',
            'navNameProfil' => 'Profil',
            'animes' => $data['data']['Page']['media'],
            'popular_animes' => $data_popular['data']['Page']['media'],
            'random_animes' => $data_random
        ]);
    }
}
