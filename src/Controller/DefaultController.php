<?php


namespace App\Controller;


use App\Entity\Video;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('App\Entity\Video')->findBy(['deleted' => 0],['id' => 'DESC']);

        return $this->render('default/index.html.twig', [
            'videos' => $videos
        ]);
    }

    /**
     * @param Request $request
     * @param Video $video
     * @return Response
     */
    public function play(Request $request, Video $video){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('App\Entity\Video')->findBy(['deleted' => 0]);

        return $this->render('default/video.html.twig', [
            'video' => $video,
            'videos' => $videos
        ]);
    }

    /**
     * @param Request $request
     * @return string|Response
     * @throws ConnectionException
     */
    public function addReproduction(Request $request){
        $getData = $request->query->all();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository('App\Entity\Video')->findOneBy(['deleted'=> 0, 'id' => $getData['video']]);
        try {
            $em->getConnection()->beginTransaction();
            $reproductions = $video->getReproductions() + 1;
            $video->setReproductions($reproductions);

            $em->flush();
            $em->getConnection()->commit();
            return new Response(
                $video->getReproductions(),
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );
        }catch (Exception $e){
            $em->getConnection()->rollback();
            return $e->getMessage();
        }
    }


    /**
     * @Route("/search", name="index_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if(!empty($data)) {
            $search = $em->getRepository('App\Entity\Video')->search($data['search']);
        }else{
            $search = $em->getRepository('App\Entity\Video')->findBy(['deleted' => 0], ['id' => 'DESC']);
        }

        return $this->render('default/index.html.twig', [
            'videos' => $search
        ]);
    }
}