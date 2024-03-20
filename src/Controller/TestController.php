<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app', name: 'app')]
class TestController extends AbstractController
{
    #[Route('/test/{id}', name: '_test', requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {

        $tab = [];


        $jours = [
            'L' => 'lundi',
            'M' => 'mardi',
            'Me' => 'mercredi'
        ];

        return $this->render('test/index.html.twig', [
            'id' => $id,
            'jours' => $jours,
            'varHtml' => '<h1>variable avec du HTML</h1>',
            'tab' => $tab
        ]);
    }
}
