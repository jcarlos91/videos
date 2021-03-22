<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user/list", name="admin_user_list")
     * @param Request $request
     * @return Response
     */
    public function userList(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository('App\Entity\User')->findBy(['deleted' => 0]);

        return $this->render('back/User/list.html.twig', [
            'users' => $user,
            'title' => 'User List',
        ]);
    }

    /**
     * @Route("/admin/user/new", name="admin_user_new")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws ConnectionException
     */
    public function userNew(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try {
                $em->getConnection()->beginTransaction();
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
                $user->setDeleted(0);
                $em->persist($user);
                $em->flush();
                $em->getConnection()->commit();
                $this->addFlash(
                    'success',
                    'Process Complete Success'
                );
                return $this->redirectToRoute('admin_user_list');
            }catch (Exception $e){
                $em->getConnection()->rollBack();
                $this->addFlash(
                    'danger',
                    $e->getMessage()
                );
                return $this->redirectToRoute('admin_user_list');
            }
        }

        return $this->render('back/User/new.html.twig',[
            'form' => $form->createView(),
            'title' => 'User New'
        ]);
    }

    /**
     * @Route("/admin/user/{user}/edit", name="admin_user_edit")
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     * @throws ConnectionException
     */
    public function userEdit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try {
                $em->getConnection()->beginTransaction();
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
                $user->setDeleted(0);
                $em->persist($user);
                $em->flush();
                $em->getConnection()->commit();
                $this->addFlash(
                    'success',
                    'Process Complete Success'
                );
                return $this->redirectToRoute('admin_user_list');
            }catch (Exception $e){
                $em->getConnection()->rollBack();
                $this->addFlash(
                    'danger',
                    $e->getMessage()
                );
                return $this->redirectToRoute('admin_user_list');
            }
        }

        return $this->render('back/User/new.html.twig',[
            'form' => $form->createView(),
            'title' => 'User Edit'
        ]);
    }

    /**
     * @Route("/admin/user/{user}/delete", name="admin_user_delete")
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws ConnectionException
     */
    public function userDelete(Request $request, User $user){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            $em->getConnection()->beginTransaction();
            $user->setDeleted(1);

            $em->flush();
            $em->getConnection()->commit();
            $this->addFlash(
                'success',
                'Process Complete Success'
            );
            return $this->redirectToRoute('admin_user_list');
        }catch (\Exception $e){
            $em->getConnection()->rollBack();
            $this->addFlash(
                'danger',
                $e->getMessage()
            );
            return $this->redirectToRoute('admin_user_list');
        }
    }
}