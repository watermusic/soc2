<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Score;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{

    /**
     * @return Response
     */
    public function indexAction()
    {
        $scoreRepo = $this->getDoctrine()->getRepository('AppBundle:Score');

        /** @var Score[] $scores */
        $scores = $scoreRepo->findAll();

        $standings = [];
        $ppd = [];
        $count = 0;
        foreach($scores as $score) {
            $name = ucfirst($score->getPlayer()->getUsername());
            if(!isset($standings[$name])) {
                $standings[$name] = 0;
            }
            $standings[$name] += $score->getScore();

            if(!isset($ppd[$name])) {
                $ppd[$name] = array();
            }
            $ppd[$name][] = $score->getScore();
            $count = count($ppd[$name]);
        }
        arsort($standings);

        $user = $this->getUser();

        $view = array(
            'title' => 'Dashboard',
            'user' => $user,
            'standings' => $standings,
            'news' => array(
                'template' => file_get_contents(__DIR__ . '/../Resources/views/Dashboard/timeline-item.html.mustache'),
            ),
            'ppd' => array(
                'labels' => json_encode(range(1, $count)),
                //'data' => json_encode($ppd[ucfirst($user->getUsername())]),
                'data' => json_encode($ppd),
            ),
        );

        return $this->render('AppBundle:Dashboard:index.html.twig', $view);
    }

    /**
     * @return JsonResponse
     */
    public function newsAction()
    {

        $rss_feed = "http://rss.kicker.de/news/bundesliga";
        $rss_data = file_get_contents($rss_feed);

        $dom = new \DOMDocument();
        $dom->loadXML($rss_data);
        $items = $dom->getElementsByTagName('item');

        $data = array();
        foreach ($items as $node) {

            /** @var \DOMDocument $node */
            $item = array (
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            );
            array_push($data, $item);
        }

        return new JsonResponse($data);
    }

    public function standingsAction()
    {
        /** @var User $baseUser */
        $baseUser = $this->getUser();

        $doctrine = $this->getDoctrine();
        $userRepository = $doctrine->getRepository('AppBundle:User');

        $gamers = $userRepository->findAll();

        $users = [];
        foreach ($gamers as $gamer) {

            $user = [
                'username' => $gamer->getUsername(),
                'players' => [
                    'Torwart' => ['size' => 0, 'amount' => 0.0],
                    'Abwehr' => ['size' => 0, 'amount' => 0.0],
                    'Mittelfeld' => ['size' => 0, 'amount' => 0.0],
                    'Sturm' => ['size' => 0, 'amount' => 0.0],
                ]
            ];

            foreach ($gamer->getPlayer() as $player) {
                /** @var Player $player */
                $user['players'][$player->getPosition()->getName()]['size'] += 1;
                $user['players'][$player->getPosition()->getName()]['amount'] += $player->getEkPreis();
            }
            
            array_push($users, $user);
        }

        return $this->render('@App/Default/standings.html.twig', [
            'title' => 'Übersicht',
            'users' => $users,
            'user' => $baseUser,
        ]);
    }

    /**
     * @return Response
     */
    public function lineupAction()
    {

        $user = $this->getUser();
        $doctrine = $this->getDoctrine();
        $teamRepository = $doctrine->getRepository('AppBundle:Team');
        $playerRepository = $doctrine->getRepository('AppBundle:Player');
        $positionenRepository = $doctrine->getRepository('AppBundle:Position');
        $matchesRepository = $doctrine->getRepository('AppBundle:Match');

        $matchDay = $matchesRepository->findNextMatchday();
        $teams = $teamRepository->findAll();
        $positionen = $positionenRepository->findAll();

        /** @var Player[] $allPlayers */
        $allPlayers = $playerRepository->findBy(array('user' => $user));
        $positionenGroups = array();

        foreach ($allPlayers as $player) {

            $posName = $player->getPosition()->getName();
            if(!isset($positionenGroups[$posName])) {
                $positionenGroups[$posName] = array();
            }
            array_push($positionenGroups[$posName], $player);
        }

        $view = array(
            'title' => 'Aufstellung',
            'user' => $user,
            'lineup' => array(),
            'teams' => $teams,
            'matchDay' => $matchDay,
            'positionen' => $positionen,
            'positionenGroup' => $positionenGroups,
            'template' => file_get_contents(__DIR__ . '/../Resources/views/Default/lineup-item.html.mustache'),
        );

        return $this->render('AppBundle:Default:lineup.html.twig', $view);
    }

    /**
     * @param int $matchday
     * @param string $_format
     * @return Response
     */
    public function lineupPrintAction($matchday, $_format)
    {
        $doctrine = $this->getDoctrine();
        $userRepository = $doctrine->getRepository('AppBundle:User');
        $playerRepository = $doctrine->getRepository('AppBundle:Player');
        $lineupRepository = $doctrine->getRepository('AppBundle:Lineup');

        $users = $userRepository->findAll();

        $lineups = array();

        foreach ($users as $user) {
            $lineups[$user->getUsername()] = array();
            $lineup = $lineupRepository->findOneBy(array('user' => $user, 'matchday' => $matchday));

            $lineups[$user->getUsername()]['players'] = array();
            $lineups[$user->getUsername()]['lineup'] = $lineup;

            if($lineup === null) {
                continue;
            }

            $data = $lineup->getData();
            foreach ($data["lineup"] as $position) {
                foreach ($position as $name => $playerId ) {
                    $player = $playerRepository->find($playerId);
                    array_push($lineups[$user->getUsername()]['players'], $player);
                }
            }

        }

        $view = array(
            'title' => sprintf('%s. Spieltag ausdrucken', $matchday),
            'lineups' => $lineups,
            'matchday' => $matchday,
        );


        if($_format === "html") {
            return $this->render('AppBundle:Default:lineup-print.html.twig', $view);
        }

        $url = $this->generateUrl('soc_lineup_print', ['matchday' => $matchday, '_format' => 'html'], UrlGeneratorInterface::ABSOLUTE_URL);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($url, [
                'orientation' => 'Landscape',
                'margin-bottom' => 0,
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-top' => 0,
                ]
            ),
            200,
            [
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => sprintf('attachment; filename="lineup_%02d.pdf"', $matchday)
            ]
        );
    }

    /**
     * @return Response
     */
    public function teamAction()
    {

        $user = $this->getUser();
        $doctrine = $this->getDoctrine();
        $playerRepository = $doctrine->getRepository('AppBundle:Player');

        $allPlayers = $playerRepository->findBy(array('user' => $user));

        $socParameter = $this->getParameter('soc');
        $playersNeeded = $socParameter['players_needed'];
        $budget = $socParameter['budget'];

        $moneySpend = 0;
        foreach ($allPlayers as $player) {
            $moneySpend += $player->getEkPreis();
        }

        $moneyLeft = ($budget - $moneySpend >= 0) ? $budget - $moneySpend : 0;
        $playersLeft = ($playersNeeded - count($allPlayers) <= 0) ? 1 : $playersNeeded - count($allPlayers);

        $moneyPerPlayer = $moneyLeft / $playersLeft;

        $view = array(
            'user' => $user,
            'players' => $allPlayers,
            'title' => 'Team von ' . ucfirst($user->getUsername()),
            'players_needed' => $playersNeeded,
            'money_spend' => $moneySpend,
            'money_left' => $moneyLeft,
            'money_per_player' => $moneyPerPlayer,
            'budget' => $budget,
        );

        return $this->render('AppBundle:Default:team.html.twig', $view);
    }

}
