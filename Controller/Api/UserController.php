<?php

namespace Melodia\UserBundle\Controller\Api;

use Engage360d\Bundle\RestBundle\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Melodia\UserBundle\Form\Type\UserFormType;
use Melodia\UserBundle\Entity\User;

class UserController extends RestController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  section="User (Администратор)",
     *  description="Получение списка пользователей.",
     *  filters={
     *      {
     *          "name"="page",
     *          "dataType"="integer",
     *          "default"=1,
     *          "required"=false
     *      },
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "default"="inf",
     *          "required"=false
     *      }
     *  }
     * )
     */
    public function getUsersAction(Request $request)
    {
        $page = $request->query->get('page') ?: 1;
        // By default this method returns all records
        $limit = $request->query->get('limit') ?: 0;

        // Check filters' format
        if (!is_numeric($page) || !is_numeric($limit)) {
            return new JsonResponse(null, 400);
        }

        // TODO order by
        $users = $this->get('doctrine')->getRepository(User::REPOSITORY)
            ->findSubset($page, $limit);

        $users = $this->get('jms_serializer')->serialize($users, 'json',
            SerializationContext::create()->setGroups(array("getAllUsers"))
        );

        return new Response($users, 200);
    }

    /**
     * @ApiDoc(
     *  section="User (Администратор)",
     *  description="Создание нового пользователя.",
     *  input="Melodia\UserBundle\Form\Type\UserFormType",
     *  output="Melodia\UserBundle\Entity\User"
     * )
     */
    public function postUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserFormType(), $user);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $user = $this->get('jms_serializer')->serialize($user, 'json',
            SerializationContext::create()->setGroups(array("getOneUser"))
        );

        return new Response($user, 201);
    }

    /**
     * @ApiDoc(
     *  section="User (Администратор)",
     *  description="Получение детальной информации о пользователе.",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "description"="User id"}
     *  }
     * )
     */
    public function getUserAction($id)
    {
        $user = $this->get('doctrine')
            ->getRepository(User::REPOSITORY)
            ->findOneBy(array("id" => $id));

        if (!$user) {
            return new JsonResponse(null, 404);
        }

        $user = $this->get('jms_serializer')->serialize($user, 'json',
            SerializationContext::create()->setGroups(array("getOneUser"))
        );

        return new Response($user, 200);
    }

    /**
     * @ApiDoc(
     *  section="User (Администратор)",
     *  description="Редактирование пользователя.",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "description"="User id"}
     *  },
     *  input="Melodia\UserBundle\Form\Type\UserFormType",
     *  output="Melodia\UserBundle\Entity\User"
     * )
     */
    public function putUserAction($id, Request $request)
    {
        $user = $this->get('doctrine')
            ->getRepository(User::REPOSITORY)
            ->findOneBy(array("id" => $id));

        if (!$user) {
            return new JsonResponse(null, 404);
        }

        $form = $this->createForm(new UserFormType(), $user);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $user = $this->get('jms_serializer')->serialize($user, 'json',
            SerializationContext::create()->setGroups(array("getOneUser"))
        );

        return new Response($user, 200);
    }

    /**
     * @ApiDoc(
     *  section="User (Администратор)",
     *  description="Удаление пользователя.",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "description"="User id"}
     *  }
     * )
     */
    public function deleteUserAction($id)
    {
        $user = $this->get('doctrine')
            ->getRepository(User::REPOSITORY)
            ->findOneBy(array("id" => $id));

        if (!$user) {
            return new JsonResponse(null, 404);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, 200);
    }
}
