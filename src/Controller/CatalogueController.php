<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue/{page}', name: 'app_catalogue')]
    public function index(int $page = 1): Response
    {
        $client = new Client();

        $response = $client->request('POST', 'https://graphql.anilist.co', [
            'headers' => [
                'content-type' => 'application/json',
            ],
            'json' => [
                'query' => '
                query ($page: Int) {
                    Page(page: $page, perPage: 25) {
                        pageInfo {
                            total
                            perPage
                            currentPage
                            lastPage
                            hasNextPage
                        }
                        media(type: ANIME, sort: POPULARITY_DESC) {
                            id
                            title {
                                romaji
                                english
                                native
                            }
                            type
                            isAdult
                            status
                            description
                            coverImage {
                                large
                            }
                            siteUrl
                        }
                    }
                }
                ',
                'variables' => [
                    'page' => $page,
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return $this->render('catalogue/index.html.twig', [
            'controller_name' => 'CatalogueController',
            'titre' => 'Catalogue',
            'navNameAccueil' => 'Accueil',
            'navNameCatalogue' => '',
            'navNameAgenda' => 'Agenda',
            'navNameProfil' => 'Profil',
            'animes' => $data['data']['Page']['media'],
            'pageInfo' => $data['data']['Page']['pageInfo'],
        ]);
    }
}
