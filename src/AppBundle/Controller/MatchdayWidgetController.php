<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MatchdayWidgetController extends Controller
{

    public function renderAction()
    {
        $matchesRepository = $this->getDoctrine()->getRepository('AppBundle:Match');
        $matchDay = $matchesRepository->findNextMatchday();

        $matches = $matchesRepository->findNextMatchdayMatches();

        return $this->render('@App/Widget/_matchdayWidget.html.twig', [
            'matches' => $matches,
        ]);
    }

}
