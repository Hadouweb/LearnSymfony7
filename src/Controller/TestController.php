<?php

namespace App\Controller;

use App\Entity\Tuto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $qB = $entityManager->createQueryBuilder();
        $qB->select('t')->from(Tuto::class, 't');

        $tutos = $qB->getQuery()->getArrayResult();

        $f = fopen('php://memory', 'w');

        foreach ($tutos as $tuto)
        {
            fputcsv($f, $tuto, ';');
        }

        fseek($f, 0);

        header('Content-Type: application/csv');
        header('Content-disposition: attachment; filename="export.csv"');
        fpassthru($f);
        die();

        return $this->render('example/index.html.twig', [
            'controller_name' => 'ExampleController',
        ]);
    }
}
