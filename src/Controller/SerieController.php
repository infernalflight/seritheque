<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'app_serie')]
class SerieController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function list(SerieRepository $serieRepository): Response
    {
        //$series = $serieRepository->findAll();
        $series = $serieRepository->findSeriesOnlyReturning();

        return $this->render('serie/index.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function detail(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }



    #[Route('/create', name: '_create')]
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
