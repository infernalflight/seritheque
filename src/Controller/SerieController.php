<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SerieController extends AbstractController
{
    #[Route('/serie/list', name: 'app_serie_list')]
    public function list(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('serie/create', name: 'app_serie_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $serie = new Serie();

        $serie->setName('Gomorra')
            ->setOverview('Une série super')
            ->setStatus('ENDED')
            ->setGenres('Thriller, Mafia');

        $em->persist($serie);
        $em->flush();


        return new Response('Une nouvelle série a été crée en base. Va vérifier !');
    }


}
