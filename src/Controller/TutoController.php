<?php

namespace App\Controller;

use App\Entity\Tuto;
use App\Repository\TutoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutoController extends AbstractController
{
    #[Route('/tuto/{slug}', name: 'app_tuto_details')]
    public function view(TutoRepository $tutoRepository, string $slug): Response
    {
        $tuto = $tutoRepository->findOneBySlug($slug);

        if (!$tuto) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('tuto/details.html.twig', [
            'tuto' => $tuto,
        ]);
    }

    #[Route('/tuto/{id}', name: 'app_tuto')]
    public function index(TutoRepository $tutoRepository, int $id): Response
    {
        $tuto = $tutoRepository->findOneById($id);
        if (!$tuto) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return $this->render('tuto/index.html.twig', [
            'controller_name' => 'TutoController',
            'name' => $tuto->getName(),
        ]);
    }

    #[Route('/add-tuto', name: 'create_tuto')]
    public function createTuto(EntityManagerInterface $entityManager): Response
    {
        $tuto = new Tuto();
        $tuto->setName('Unity3D');
        $tuto->setSlug('tuto-unity3d');
        $tuto->setDescription('Lorem ipsum');
        $tuto->setSubtitle('Lorem ipsum');
        $tuto->setImage('midjourney.png');
        $tuto->setVideo('RAZjcibFE1A');
        $tuto->setLink('https://www.youtube.com/watch?v=RAZjcibFE1A&ab_channel=TUTOUNITYFR');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($tuto);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$tuto->getId());
    }
}
