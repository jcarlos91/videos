<?php


namespace App\Controller;


use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/admin/video/list", name="admin_video_list")
     * @param Request $request
     * @return Response
     */
    public function videoList(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Video $video */
        $videos = $em->getRepository('App\Entity\Video')->findBy(['deleted' => 0]);

        return $this->render('back/Video/list.html.twig', [
            'videos' => $videos,
            'title' => 'Videos',
            'subtitle' => 'Video List'
        ]);
    }

    /**
     * @Route("/admin/video/new", name="admin_video_new")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ConnectionException
     */
    public function videoNew(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Video $video */
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST') {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $em->getConnection()->beginTransaction();
                    $urlForm = $form->get('url')->getData();
                    $url = str_replace('embed', 'download', $urlForm);
                    $video->setUrl($url);
                    $video->setReproductions(0);
                    $video->setDeleted(0);
                    $em->persist($video);
                    $em->flush();
                    $em->getConnection()->commit();

                    $this->addFlash(
                        'success',
                        'Process Complete Success'
                    );
                    return $this->redirectToRoute('admin_video_list');
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                    $this->addFlash(
                        'danger',
                        $e->getMessage()
                    );
                    return $this->redirectToRoute('admin_video_list');
                }
            }
        }

        return $this->render('back/Video/new.html.twig',[
            'form' => $form->createView(),
            'title' => 'Videos Loader',
        ]);
    }

    /**
     * @Route("/admin/video/{video}/edit", name="admin_video_edit")
     * @param Request $request
     * @param Video $video
     * @return RedirectResponse|Response
     * @throws ConnectionException
     */
    public function videoEdit(Request $request, Video $video){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try {
                $em->getConnection()->beginTransaction();
                $em->flush();
                $em->getConnection()->commit();
                $this->addFlash(
                    'success',
                    'Process Complete Success'
                );
                return $this->redirectToRoute('admin_video_list');
            }catch (\Exception $e){
                $em->getConnection()->rollBack();
                $this->addFlash(
                    'danger',
                    $e->getMessage()
                );
                return $this->redirectToRoute('admin_video_list');
            }
        }

        return $this->render('back/Video/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Video Edition'
        ]);
    }

    /**
     * @Route("/admin/video/{video}/deleted", name="admin_video_deleted")
     * @param Request $request
     * @param Video $video
     * @return RedirectResponse
     * @throws ConnectionException
     */
    public function videoDelete(Request $request, Video $video){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try{
            $em->getConnection()->beginTransaction();
            $video->setDeleted(1);

            $em->flush();
            $em->getConnection()->commit();
            $this->addFlash(
                'success',
                'Process Complete Success'
            );
            return $this->redirectToRoute('admin_video_list');
        }catch (\Exception $e){
            $em->getConnection()->rollBack();
            $this->addFlash(
                'danger',
                $e->getMessage()
            );
            return $this->redirectToRoute('admin_video_list');
        }
    }

}