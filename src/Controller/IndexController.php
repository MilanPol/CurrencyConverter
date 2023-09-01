<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'main', methods: ['GET'])]
    public function list(): Response
    {
        return $this->redirectToRoute('dashboard');
    }
}
