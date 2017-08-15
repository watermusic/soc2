<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MatchdayWidgetController extends Controller
{

    public function renderAction()
    {
        $matchesRepository = $this->getDoctrine()->getRepository('AppBundle:Match');
        $matches = $matchesRepository->findNextMatchdayMatches();

        return $this->render('@App/Widget/_matchdayWidget.html.twig', [
            'matches' => $matches,
        ]);
    }

}
