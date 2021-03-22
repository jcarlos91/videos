<?php


namespace App\Controller;


use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackEndController extends AbstractController
{
    /**
     * @Route("/admin/", name="admin")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $videoRepository = $em->getRepository('App\Entity\Video');
        $userRepository = $em->getRepository('App\Entity\User');
        $activeVideo = $videoRepository->getActiveVideos();
        $numReproductions = $videoRepository->getNumReproductions();
        $userActive = $userRepository->getAllUserActive();

        return $this->render('back/default/index.html.twig', [
            'activeVideo' => $activeVideo,
            'numReproductions' => $numReproductions,
            'activeUser' => $userActive
        ]);
    }

}