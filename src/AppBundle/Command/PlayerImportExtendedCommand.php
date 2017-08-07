<?php

namespace AppBundle\Command;

use AppBundle\Entity\Player;
use AppBundle\Entity\Position;
use AppBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Command that lists the file keys of a filesystem
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class PlayerImportExtendedCommand extends ContainerAwareCommand
{

    protected $kickerSite = "http://manager.kicker.de/classic/bundesliga/meinteam/meinkader";

    protected $positionen = array(
        'Torwart' => array(
            'uri' => 'http://manager.kicker.de/classic/bundesliga/meinteam/ajax.ashx?ajaxtype=spielerauswahl&position=1&verein=0&sort=1&list=0&playerSearchStr=',
            'shortcut' => 'TW',
            'colorName' => 'danger',
        ),
        'Abwehr' => array(
            'uri' => 'http://manager.kicker.de/classic/bundesliga/meinteam/ajax.ashx?ajaxtype=spielerauswahl&position=2&verein=0&sort=1&list=0&playerSearchStr=',
            'shortcut' => 'AB',
            'colorName' => 'success',
        ),
        'Mittelfeld' => array(
            'uri' => 'http://manager.kicker.de/classic/bundesliga/meinteam/ajax.ashx?ajaxtype=spielerauswahl&position=3&verein=0&sort=1&list=0&playerSearchStr=',
            'shortcut' => 'MF',
            'colorName' => 'warning',
        ),
        'Sturm' => array(
            'uri' => 'http://manager.kicker.de/classic/bundesliga/meinteam/ajax.ashx?ajaxtype=spielerauswahl&position=4&verein=0&sort=1&list=0&playerSearchStr=',
            'shortcut' => 'ST',
            'colorName' => 'primary',
        ),
    );

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('soc:player:import')
            ->setDescription('Imports Player from the Kicker.de Website in Storage')
            ->setHelp(
                <<<EOT
                The <info>soc:player:import</info> Imports Player from the Kicker.de Website in Storage

    <info>./app/console soc:player:import</info>

    Options:
    - clear
EOT
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $doctrine = $this->getContainer()->get('doctrine');
        $connection = $doctrine->getConnection();

        $connection->executeQuery('SET foreign_key_checks = 0');
        $connection->executeQuery('TRUNCATE soc_player');
        $connection->executeQuery('TRUNCATE soc_team');
        $connection->executeQuery('TRUNCATE soc_position');
        $connection->executeQuery('SET foreign_key_checks = 1');

        $this->updateTeams();
        $this->updatePositions();
        $this->updatePlayers();

    }

    private function getPlayers($position)
    {

        $decorator = <<<'HTML'
<!DOCTYPE html>
<html>
    <body>
        <div class="list">%s</div>
    </body>
</html>
HTML;

        $playerResult = array();

        /** @var string $uri */
        $uri = $this->positionen[$position]['uri'];
        $body = file_get_contents($uri);
        $html = sprintf($decorator, $body);

        $crawler = new Crawler($html);

        $players = $crawler->filter("div.pli");

        foreach($players as $player) {

            $player = new Crawler($player);

            $result = array();
            $result["name"] = $player->filter("a")->extract("_text")[0];
            $result["verein"] = utf8_decode($player->filter("span.vrn")->extract("_text")[0]);
            $result["note"] = 3.5;
            $result["vk_preis"] = str_replace(",", "", $player->filter("span.wert b")->extract("_text")[0]) * 100000;
            $result["punkte"] = (float)$player->filter("span.pkt b")->extract("_text")[0];
            $result["thumb_url"] = $player->filter("img")->extract("src")[0];
            $result["position"] = $position;

            $result["verein"] = str_replace(
                array("VfB", "HSV", "E. Frankfurt"),
                array("Stuttgart", "Hamburg", "Frankfurt"),
                $result["verein"]
            );

            array_push($playerResult, $result);
        }

        return $playerResult;
    }


    private function updatePlayers()
    {
        $doctrine = $this->getContainer()->get("doctrine");
        $om = $doctrine->getManager();

        $teamRepo = $doctrine->getRepository('AppBundle:Team');
        $teams = $teamRepo->findAll();
        $positionRepo = $doctrine->getRepository('AppBundle:Position');
        $positions = $positionRepo->findAll();

        foreach($this->positionen as $position => $posData) {

            $players = $this->getPlayers($position);

            foreach($players as $player) {

                $t = null;
                foreach($teams as $team) {
                    /** @var Team $team */
                    if($team->getName() === $player["verein"]) {
                        $t = $team;
                    }
                }

                $p = null;
                foreach($positions as $position) {
                    /** @var Position $position */
                    if($position->getName() === $player["position"]) {
                        $p = $position;
                    }
                }

                $entity = new Player();
                $entity
                    ->setName(utf8_decode($player["name"]))
                    ->setTeam($t)
                    ->setNote($player["note"])
                    ->setVkPreis($player["vk_preis"])
                    ->setEkPreis(0.0)
                    ->setPosition($p)
                    ->setThumbUrl($player["thumb_url"])
                    ->setPunkte($player["punkte"]);

                $om->persist($entity);
            }
            $om->flush();

        }
    }

    private function updateTeams()
    {
        $doctrine = $this->getContainer()->get("doctrine");
        $om = $doctrine->getManager();

        reset($this->positionen);
        $position = key($this->positionen);

        $players = $this->getPlayers($position);
        $vereine = array();
        foreach($players as $player) {
            if(!in_array($player["verein"], $vereine)) {
                array_push($vereine, $player["verein"]);
            }
        }

        asort($vereine);

        foreach($vereine as $verein) {
            $team = new Team();
            $team->setName($verein);
            $om->persist($team);
        }
        $om->flush();

    }

    private function updatePositions()
    {
        $doctrine = $this->getContainer()->get("doctrine");
        $om = $doctrine->getManager();

        foreach($this->positionen as $positionName => $position) {
            $entity = new Position();
            $entity
                ->setName($positionName)
                ->setColorName($position['colorName'])
                ->setShortcut($position['shortcut'])
                ;
            $om->persist($entity);
        }
        $om->flush();
    }

}
