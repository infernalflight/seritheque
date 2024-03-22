<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'app_serie')]
class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: '_list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function list(SerieRepository $serieRepository, int $page): Response
    {
        if ($page < 1) {
            throw new NotFoundHttpException('impossible');
        }

        $offset = ($page - 1) * 20;

        //$series = $serieRepository->findAll();
        //$nbSeriesMAx = $serieRepository->count();

        $series = $serieRepository->findBy(['status' => 'returning'], ['firstAirDate' => 'DESC'], 20, $offset);
        $nbSeriesMax = $serieRepository->count(['status' => 'returning']);

        // Requete construite avec QueryBuilder
        //$series = $serieRepository->findSeriesOnlyReturning($offset);
        //$nbSeriesMax = $serieRepository->findSeriesOnlyReturning();

        // Requete faite avec DQL
        //$series = $serieRepository->findSeriesWithDql();

        // Requete avec Sql
        //$series = $serieRepository->findSeriesWithSql($offset);
        //$nbSeriesMax = $series[0]['nbMax'];

        $pagesMax = ceil($nbSeriesMax / 20);

        return $this->render('serie/index.html.twig', [
            'series' => $series,
            'currentPage' => $page,
            'pagesMax' => $pagesMax,
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
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'La série a été créée. A +');

            return $this->redirectToRoute('app_serie_list');
        }

        return $this->render('serie/edit.html.twig', [
            'serieForm' => $form,
        ]);
    }



    #[Route('/createOld', name: '_createOld')]
    public function createOld(EntityManagerInterface $em): Response
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
