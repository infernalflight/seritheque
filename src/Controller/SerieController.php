<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/serie', name: 'app_serie')]
#[IsGranted('ROLE_USER')]
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

        //$series = $serieRepository->findBy(['status' => 'returning'], ['firstAirDate' => 'DESC'], 20, $offset);
        //$nbSeriesMax = $serieRepository->count(['status' => 'returning']);

        // Requete construite avec QueryBuilder
        $series = $serieRepository->findSeriesOnlyReturning($offset);
        $nbSeriesMax = $serieRepository->findSeriesOnlyReturning();

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
    public function detail(Serie $serie): Response
    {

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie
        ]);
    }


    #[Route('/create', name: '_create')]
    #[IsGranted('ROLE_CONTRIB')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $posterFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($serie->getName()).'-'.uniqid() . '.'.$posterFile->guessExtension();
                $posterFile->move('posters/series', $fileName);
                $serie->setPoster($fileName);
            }

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'La série a été créée. A +');

            return $this->redirectToRoute('app_serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serieForm' => $form,
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_CONTRIB')]
    public function update(Serie $serie, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(SerieType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->has('delete_image') && $form->get('delete_image')->getData()) {
                $serie->deleteImage();
                $serie->setPoster(null);
            }

            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $posterFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($serie->getName()).'-'.uniqid() . '.'.$posterFile->guessExtension();
                $posterFile->move('posters/series', $fileName);

                $serie->deleteImage();

                $serie->setPoster($fileName);
            }

            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'La série a été modifiée');

            return $this->redirectToRoute('app_serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serieForm' => $form,
            'serie' => $serie,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Serie $serie, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->get('_token'))) {
            $em->remove($serie);
            $em->flush();
            $this->addFlash('success', 'Une série a été supprimée');
        }

        return $this->redirectToRoute('app_serie_list');
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
