<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

class AgendaController extends AbstractController
{
    #[Route('/agenda', name: 'app_agenda')]
    public function index(): Response
    {
        // Calcule le timestamp Unix pour le début et la fin de la semaine
        $startOfWeek = (new \DateTime('monday this week'))->getTimestamp();
        $endOfWeek = (new \DateTime('sunday this week'))->getTimestamp();

        $client = new Client();

        $response = $client->request('POST', 'https://graphql.anilist.co', [
            'headers' => [
                'content-type' => 'application/json',
            ],
            'json' => [
                'query' => '
                query ($startOfWeek: Int, $endOfWeek: Int) {
                    Page {
                        airingSchedules(airingAt_greater: $startOfWeek, airingAt_lesser: $endOfWeek) {
                            id
                            airingAt
                            episode
                            media  {
                                id
                                title {
                                    romaji
                                    english
                                    native
                                }
                                type
                                isAdult
                                status
                                coverImage {
                                    large
                                }
                                siteUrl
                            }
                        }
                    }
                }
                ',
                'variables' => [
                    'startOfWeek' => $startOfWeek,
                    'endOfWeek' => $endOfWeek,
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Convertit la liste en un tableau associatif avec des jours de la semaine comme clés
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $animes = [];
        foreach ($weekDays as $day) {
            $animes[$day] = [];
        }

        foreach ($data['data']['Page']['airingSchedules'] as $anime) {
            if ($anime['media']['isAdult'] === false && $anime['media']['type'] === 'ANIME') { // Ajoute uniquement les animes qui ne sont pas marqués comme adultes
                $dayOfWeek = date('l', $anime['airingAt']);
                $animes[$dayOfWeek][] = $anime;
            }
        }

        return $this->render('agenda/index.html.twig', [
            'controller_name' => 'AgendaController',
            'titre' => 'Agenda',
            'navNameAccueil' => 'Accueil',
            'navNameCatalogue' => 'Catalogue',
            'navNameAgenda' => '',
            'navNameProfil' => 'Profil',
            'animes' => $animes,
        ]);
    }
}
