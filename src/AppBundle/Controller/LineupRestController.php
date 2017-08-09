<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lineup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/lineup")
 */
class LineupRestController extends Controller
{

    /**
     * The service endpoint
     *
     * @return JsonResponse
     */
    public function indexAction()
    {
        $data = array("msg" => "This is the service endpoint");
        $status = 200;

        return new JsonResponse($data, $status);
    }

    /**
     * Return the overall user list.
     *
     * @param string $username
     * @param $id
     * @return Response
     */
    public function getLineupAction($username, $id)
    {
        $lineupRepository = $this->getDoctrine()->getRepository('AppBundle:Lineup');
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');

        $user = $userRepository->findOneBy(array('username' => $username));

        if ($user === null) {
            throw $this->createNotFoundException('User not found.');
        }

        $criteria = array(
            'user' => $user,
            'matchday' => $id,
        );
        $entity = $lineupRepository->findOneBy($criteria);

        if (!$entity) {
            return $this->createNotFoundResponse(sprintf('Lineup could not be found. [Matchday:%s]', $id));
        }

        $data = $this->get('jms_serializer')->serialize($entity, 'json');
        $status = 200;

        return new Response($data, $status, array('Content-Type' => 'application/json'));

    }

    /**
     * Return the overall user list.
     *
     * @param string $username
     * @param Request $request
     * @return Response
     */
    public function postLineupAction($username, Request $request)
    {
        $doctrine = $this->getDoctrine();
        $om = $doctrine->getManager();
        $lineupRepository = $doctrine->getRepository('AppBundle:Lineup');
        $userRepository = $doctrine->getRepository('AppBundle:User');

        $user = $userRepository->findOneBy(array('username' => $username));

        if ($user === null) {
            throw $this->createNotFoundException(sprintf('User not found. [username:%s]', $username));
        }

        $parameters = array();
        if ($content = $request->getContent()) {
            $parameters = json_decode($content, true);
        }

        $errors = array();
        $matchday = $parameters['matchday'];
        $data = $parameters['data'];


        if($matchday === null) {
            array_push($errors, array('matchday' => "can't be blank"));
        }

        if($data === null) {
            array_push($errors, array('data' => "can't be blank"));
        }

        if(count($errors) > 0) {
            $this->createUnprocessableEntityResponse('Invalid lineup resource. Please fix errors and try again.', $errors);
        }

        $lineup = $lineupRepository->findOneBy(array('user' => $user, 'matchday' => $matchday));
        if ($lineup === null) {
            $lineup = new Lineup();
        }

        $lineup
            ->setUser($user)
            ->setMatchday($matchday)
            ->setData($data)
            ;

        $om->persist($lineup);
        $om->flush();

        $status = 201;
        $result = $this->get('jms_serializer')->serialize($lineup, 'json');
        return new Response($result, $status, array('Content-Type' => 'application/json'));

    }


    /**
     * Return the overall user list.
     *
     * @param string $username
     * @return Response
     */
    public function getUserAction($username)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy(array('username' => $username));

        if ($user === null) {
            throw $this->createNotFoundException('User not found.');
        }

        $data = $this->get('jms_serializer')->serialize($user, 'json');
        $status = 200;

        return new Response($data, $status, array('Content-Type' => 'application/json'));

    }

    /**
     * @param string  $message
     * @param array $errors
     * @return JsonResponse
     */
    protected function createUnprocessableEntityResponse($message, $errors)
    {
        $data = array(
            'error' => $message,
            'errors' => $errors
        );
        return new JsonResponse($data, 422);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function createNotFoundResponse($message)
    {
        $data = array(
            'error' => $message
        );
        return new JsonResponse($data, 404);
    }

}