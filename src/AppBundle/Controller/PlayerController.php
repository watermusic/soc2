<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Player controller.
 *
 */
class PlayerController extends Controller
{

    /**
     * Lists all Player entities.
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if(!$request->query->has("order")) {
            $request->query->add(array("order" => "vkPreis"));
        }

        if(!$request->query->has("dir")) {
            $request->query->add(array("dir" => "DESC"));
        }

        $query = $this->getQuery($request);

        $queryArray = $request->query->all();

        $vkPreis = $request->get("order", "vkPreis");
        $dir = $request->get("dir", "DESC");

        $kaeufer = false;
        $extras = array();
        $criteria = array();

        foreach($query as $name => $val) {

            if($name === "order"  || $name === "dir") {
                continue;
            }

            if($val === null || $val === "" || $val === '- alle -') {
                continue;
            }

            if($name === "kaeufer") {
                $kaeufer = true;
            }

            $criteria[$name] = $val;
        }

        $entities = $em->getRepository('AppBundle:Player')->findBy($criteria, array($vkPreis => $dir));

        if($kaeufer === true) {

            $money_spent = 0;
            $players_count = 0;
            /**
             * @var $player Player
             */
            foreach($entities as $player) {
                $money_spent += $player->getEkPreis();
                $players_count += 1;
            }

            $players_left = $this->container->getParameter("soc_players_to_buy") - $players_count;

            if($players_left <= 0 ) {
                $players_left = 1;
            }

            $number_of_players = count($entities);
            $money_total = $this->container->getParameter("soc_amount");
            $money_left = $money_total - $money_spent;
            $money_per_player = ($money_left > 0) ? $money_left / $players_left : 0;


            $extras = array(
                "number_of_players" => $number_of_players,
                "money_total" => $money_total,
                "money_left" => $money_left,
                "money_per_player" => $money_per_player,
            );

        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('AppBundle:Player:index.html.twig', array(
            'entities' => $entities,
            'statics' => $this->getStaticViewParameter(),
            "query" => $query,
            "extras" => $extras,
            "queryArray" => $queryArray,
            "user" => $user,
        ));
    }

    /**
     * Creates a new Player entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $entity = new Player();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('soc_player_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Player:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'statics' => $this->getStaticViewParameter(),
            "query" => $this->getQuery($request),
            "user" => $this->getCurrentUser(),
        ));
    }

    /**
     * Creates a form to create a Player entity.
     *
     * @param Player $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Player $entity)
    {

        $statics = $this->getStaticViewParameter();

        $form = $this->createForm(new PlayerType($statics), $entity, array(
            'action' => $this->generateUrl('soc_player_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Player entity.
     *
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $entity = new Player();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Player:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'statics' => $this->getStaticViewParameter(),
            "query" => $this->getQuery($request),
            "user" => $this->getCurrentUser(),
        ));
    }

    /**
     * Finds and displays a Player entity.
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function showAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Player')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Player entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Player:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'statics' => $this->getStaticViewParameter(),
            "query" => $this->getQuery($request),
            "user" => $this->getCurrentUser(),
        ));
    }

    /**
     * Displays a form to edit an existing Player entity.
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Player')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Player entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Player:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'statics' => $this->getStaticViewParameter(),
            "query" => $this->getQuery($request),
            "user" => $this->getCurrentUser(),
        ));
    }

    /**
    * Creates a form to edit a Player entity.
    *
    * @param Player $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Player $entity)
    {

        $statics = $this->getStaticViewParameter();

        $form = $this->createForm(new PlayerType($statics), $entity, array(
            'action' => $this->generateUrl('soc_player_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Player entity.
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Player')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Player entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('soc_player_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:Player:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'statics' => $this->getStaticViewParameter(),
            "query" => $this->getQuery($request),
        ));
    }

    /**
     * Deletes a Player entity.
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Player')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Player entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('soc_player_show'));
    }

    /**
     * Search a Soc entity
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request) {

        $search = $request->get('query');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:Player')->createQueryBuilder('p');
        $query = $qb->where(
            $qb->expr()->like('p.name', ':search')
        )
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();

        $entities = $query->getResult();

        $result = array(
            "query" => $search,
            "suggestions" => array(),
        );
        foreach ($entities as $entity) {
            /** @var Player[] $entity */
            array_push($result["suggestions"], $entity->getName());
        }

        return new JsonResponse($result);

    }

    /**
     * Creates a form to delete a Player entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('soc_player_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * @return array
     */
    private function getStaticViewParameter() {

        $userManager = $this->container->get("fos_user.user_manager");

        $players = [];
        $users = $userManager->findUsers();
        foreach ($users as $user) {
            /**
             * @var $user User
             */
            $players[] = $user->getUsername();
        }


        $teamRepo = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teams = $teamRepo->findAll();

        $posRepo = $this->getDoctrine()->getRepository('AppBundle:Position');
        $positionen = $posRepo->findAll();

        $vereine = array("- alle -");
        foreach($teams as $team) {
            array_push($vereine, $team->getName());
        }

        $result = array();
        $result["positionen"] = $positionen;
        $result["spieler"] = $players;
        $result["vereine"] = $vereine;

        return $result;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getQuery($request) {

        $query = array();
        $query['kaeufer'] = $request->get("kaeufer", null);
        $query['verein'] = $request->get("verein", null);
        $query['position'] = $request->get("position", null);
        $query['name'] = $request->get("name", null);

        return $query;
    }

    /**
     * @return mixed
     */
    private function getCurrentUser() {
        return $this->get('security.token_storage')->getToken()->getUser();
    }

}
