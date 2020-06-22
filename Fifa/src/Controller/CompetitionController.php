<?php

namespace App\Controller;

use App\Entity\Continent;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionController extends AbstractController
{
    public function game($team1, $team2, $type = "elimination")
    {
        $team = [];
        $luckTeam1 = 0;
        $luckTeam2 = 0;
        $luckGame = $team1->getValue() - $team2->getValue();
        switch ($luckGame) {
            case 0:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 2);
                break;
            case 1:
                $luckTeam1 = rand(0, 3);
                $luckTeam2 = rand(0, 2);
                break;
            case 2:
                $luckTeam1 = rand(0, 4);
                $luckTeam2 = rand(0, 2);
                break;
            case 3:
                $luckTeam1 = rand(0, 5);
                $luckTeam2 = rand(0, 2);
                break;
            case 4:
                $luckTeam1 = rand(0, 6);
                $luckTeam2 = rand(0, 2);
                break;
            case 5:
                $luckTeam1 = rand(0, 7);
                $luckTeam2 = rand(0, 2);
                break;
            case 6:
                $luckTeam1 = rand(0, 8);
                $luckTeam2 = rand(0, 2);
                break;
            case 7:
                $luckTeam1 = rand(0, 9);
                $luckTeam2 = rand(0, 2);
                break;
            case 8:
                $luckTeam1 = rand(0, 9);
                $luckTeam2 = rand(0, 1);
                break;
            case 9:
                $luckTeam1 = rand(0, 9);
                $luckTeam2 = rand(0, 0);
                break;
            case -1:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 3);
                break;
            case -2:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 4);
                break;
            case -3:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 5);
                break;
            case -4:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 6);
                break;
            case -5:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 7);
                break;
            case -6:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 8);
                break;
            case -7:
                $luckTeam1 = rand(0, 2);
                $luckTeam2 = rand(0, 9);
                break;
            case -8:
                $luckTeam1 = rand(0, 1);
                $luckTeam2 = rand(0, 9);
                break;
            case -9:
                $luckTeam1 = rand(0, 0);
                $luckTeam2 = rand(0, 9);
                break;
            default:
                # code...
                break;
        }
        $team[0] = $team1;
        $team[1] = $luckTeam1;
        $team[2] = $luckTeam2;
        $team[3] = $team2;
        if ($luckTeam1 > $luckTeam2) {
            $team[4] = "victoire " . $team1->getName();
            $team[5] = $team1;
            $team[6] = $team2;
        } else if ($luckTeam1 < $luckTeam2) {
            $team[4] = "victoire " . $team2->getName();
            $team[5] = $team2;
            $team[6] = $team1;
        } else {
            $luckPeno = rand(0, 1);
            if ($luckPeno == 0) {
                if ($type == "elimination") {
                    $team[4] = "victoire " . $team1->getName() . " au penalty";
                } else {
                    $team[4] = "Match nul";
                }
                $team[5] = $team1;
                $team[6] = $team2;
            } else {
                if ($type == "elimination") {
                    $team[4] = "victoire " . $team2->getName() . " au penalty";
                } else {
                    $team[4] = "Match nul";
                }
                $team[5] = $team2;
                $team[6] = $team1;
            }
        }

        return $team;
    }

    public function point($team, $point)
    {
        $manager = $this->getDoctrine()->getManager();
        $repoTeam = $this->getDoctrine()->getRepository(Team::class);
        $team = $repoTeam->findBy([
            'name' => $team->getName()
        ]);
        $team = $team[0];
        $team->setScoring($team->getScoring() + $point);
        $manager->persist($team);
        $manager->flush();
    }

    /**
     * @Route("/competition/oceanie", name="oceanie")
     */
    public function oceanie()
    {
        $game = "";
        if (isset($_POST['submit'])) {
            // Recupere le continent océanie
            $repoOceanie = $this->getDoctrine()->getRepository(Continent::class);
            $oceanie = $repoOceanie->findBy([
                'name' => 'oceanie'
            ]);

            // Recupere les équipes d'oceanie
            $repoTeamOceanie = $this->getDoctrine()->getRepository(Team::class);
            $teamOceanie = $repoTeamOceanie->findBy([
                'continent' => $oceanie
            ]);

            // Appelle la fonction game pour simuler un match
            $game = $this->game($teamOceanie[0], $teamOceanie[1]);

            // Point du vainqueur de la coupe d'océanie
            $this->point($game[5], 10);

            // Point du deuxiéme de la coupe d'océanie
            $this->point($game[6], 5);
        }

        return $this->render('competition/oceanie.html.twig', [
            "game" => $game
        ]);
    }


    /**
     * @Route("/competition/asie", name="asie")
     */
    public function asie()
    {
        $gameDemi = [];
        $demi = [];
        $gameFinal = [];
        if (isset($_POST['submit'])) {
            // Recupere le continent asie
            $repoAsie = $this->getDoctrine()->getRepository(Continent::class);
            $asie = $repoAsie->findBy([
                'name' => 'asie'
            ]);

            // Recupere les équipes d'asie
            $repoTeamAsie = $this->getDoctrine()->getRepository(Team::class);
            $teamAsie = $repoTeamAsie->country($asie);
            // liste contenant les demi final
            $demi = [];
            // 2 premieres équipes asie au classement chapeau 1 et les 2 dernieres chapeau 2
            foreach ($teamAsie as $key => $value) {
                if ($key <= 1) {
                    $demi[0][] = $value;
                } else {
                    $demi[1][] = $value;
                }
            }
            shuffle($demi[0]);
            shuffle($demi[1]);
            dd($demi);
            $gameDemi[0] = $this->game($demi[0][0], $demi[1][0]);
            $gameDemi[1] = $this->game($demi[0][1], $demi[1][1]);

            // points pour les eliminé de demi final
            $this->point($gameDemi[0][6], 5);
            $this->point($gameDemi[1][6], 5);

            $final[0] = $gameDemi[0][5];
            $final[1] = $gameDemi[1][5];

            $gameFinal = $this->game($final[0], $final[1]);
            // points pour le perdant en final
            $this->point($gameFinal[6], 10);
            // points pour le gagnant de la competition
            $this->point($gameFinal[5], 20);
        }

        return $this->render('competition/asie.html.twig', [
            'demi' => $demi,
            'gameDemi' => $gameDemi,
            'gameFinal' => $gameFinal
        ]);
    }

    /**
     * @Route("/competition/afrique", name="afrique")
     */
    public function afrique()
    {
        $quart = [];
        $gameQuart = [];
        $demi = [];
        $gameDemi = [];
        $final = [];
        $gameFinal = [];
        if (isset($_POST['submit'])) {
            $repoAfrique = $this->getDoctrine()->getRepository(Continent::class);
            $afrique = $repoAfrique->findBy([
                'name' => 'afrique'
            ]);

            // Recupere les équipes d'asie
            $repoTeamAfrique = $this->getDoctrine()->getRepository(Team::class);
            $teamAfrique = $repoTeamAfrique->country($afrique);

            foreach ($teamAfrique as $key => $value) {
                if ($key <= 3) {
                    $quart[0][] = $value;
                } else {
                    $quart[1][] = $value;
                }
            }

            shuffle($quart[0]);
            shuffle($quart[1]);

            // simule les 4 quarts
            for ($i = 0; $i < 4; $i++) {
                $gameQuart[$i] = $this->game($quart[0][$i], $quart[1][$i]);
                $this->point($gameQuart[$i][6], 5);
            }


            // creation des demi finals
            foreach ($gameQuart as $key => $value) {
                if ($key <= 1) {
                    $demi[0][] = $value[5];
                } else {
                    $demi[1][] = $value[5];
                }
            }

            $gameDemi[0] = $this->game($demi[0][0], $demi[0][1]);
            $gameDemi[1] = $this->game($demi[1][0], $demi[1][1]);

            $this->point($gameDemi[0][6], 10);
            $this->point($gameDemi[1][6], 10);

            $final[0] = $gameDemi[0][5];
            $final[1] = $gameDemi[1][5];

            $gameFinal = $this->game($final[0], $final[1]);

            $this->point($gameFinal[6], 20);
            $this->point($gameFinal[5], 30);
        }
        return $this->render('competition/afrique.html.twig', [
            'gameDemi' => $gameDemi,
            'gameQuart' => $gameQuart,
            'gameFinal' => $gameFinal
        ]);
    }

    /**
     * @Route("/competition/amerique", name="amerique")
     */
    public function amerique()
    {
        $chapeau = [];
        $poule = [];
        $gamePoule = [];
        $gameQuart = [];
        $gameDemi = [];
        $gameFinal = [];
        if (isset($_POST['submit'])) {
            $repoAmerique = $this->getDoctrine()->getRepository(Continent::class);
            $amerique = $repoAmerique->findBy([
                'name' => 'amerique'
            ]);

            $repoTeamAmerique = $this->getDoctrine()->getRepository(Team::class);
            $teamAmerique = $repoTeamAmerique->country($amerique);

            // constitue les chapeaux
            foreach ($teamAmerique as $key => $value) {
                if ($key <= 2) {
                    $chapeau[0][] = $value;
                } else if ($key <= 5) {
                    $chapeau[1][] = $value;
                } else if ($key <= 8) {
                    $chapeau[2][] = $value;
                } else if ($key <= 11) {
                    $chapeau[3][] = $value;
                } else {
                    $chapeau[4][] = $value;
                }
            }


            foreach ($chapeau as $key => $value) {
                shuffle($chapeau[$key]);
            }


            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    if ($i != 2 || $j != 4) {
                        $poule[$i][$j][0] = $chapeau[$j][$i];
                        $poule[$i][$j][1] = 0;
                        $poule[$i][$j][2] = 0;
                        $poule[$i][$j][3] = 0;
                    }
                }
            }

            $count = 0;
            for ($i = 0; $i < 3; $i++) {
                for ($k = 0; $k <= 3; $k++) {
                    for ($m = 1 + $count; $m <= 4; $m++) {
                        if ($k != $m) {
                            if ($i != 2 || $m != 4) {
                                $gamePoule[$i][] = $this->game($poule[$i][$k][0], $poule[$i][$m][0], "poule");
                            }
                        }
                    }
                    $count += 1;
                }
                $count = 0;
            }

            for ($i = 0; $i < 3; $i++) {
                foreach ($gamePoule[$i] as $count => $game) {
                    foreach ($poule[$i] as $key => $value) {
                        if ($game[1] != $game[2]) {
                            // le gagnant $game[5]
                            if ($value[0] == $game[5]) {
                                if ($game[1] > $game[2]) {
                                    $poule[$i][$key][2] += $game[1];
                                    $poule[$i][$key][3] += $game[2];
                                } else {
                                    $poule[$i][$key][2] += $game[2];
                                    $poule[$i][$key][3] += $game[1];
                                }
                                $poule[$i][$key][1] += 3;
                            }
                            if ($value[0] == $game[6]) {
                                if ($game[1] < $game[2]) {
                                    $poule[$i][$key][2] += $game[1];
                                    $poule[$i][$key][3] += $game[2];
                                } else {
                                    $poule[$i][$key][2] += $game[2];
                                    $poule[$i][$key][3] += $game[1];
                                }
                            }
                        } else {
                            if ($value[0] == $game[0]) {
                                $poule[$i][$key][1] += 1;
                                $poule[$i][$key][2] += $game[1];
                                $poule[$i][$key][3] += $game[2];
                            }
                            if ($value[0] == $game[3]) {
                                $poule[$i][$key][1] += 1;
                                $poule[$i][$key][2] += $game[1];
                                $poule[$i][$key][3] += $game[2];
                            }
                        }
                    }
                }
            }

            foreach ($poule as $key => $value) {
                foreach ($poule[$key] as $count => $values) {
                    foreach ($poule[$key][$count] as $keys => $value) {
                        if ($keys < (count($poule[$key])) - 1) {
                            if ($poule[$key][$keys + 1][1] > $poule[$key][$keys][1]) {
                                $remp = $poule[$key][$keys];
                                $poule[$key][$keys] = $poule[$key][$keys + 1];
                                $poule[$key][$keys + 1] = $remp;
                            } else if ($poule[$key][$keys + 1][1] == $poule[$key][$keys][1]) {
                                if (($poule[$key][$keys + 1][2] - $poule[$key][$keys + 1][3]) > ($poule[$key][$keys][2] - $poule[$key][$keys][3])) {
                                    $remp = $poule[$key][$keys];
                                    $poule[$key][$keys] = $poule[$key][$keys + 1];
                                    $poule[$key][$keys + 1] = $remp;
                                } elseif ($poule[$key][$keys + 1][2] > $poule[$key][$keys][2]) {
                                    $remp = $poule[$key][$keys];
                                    $poule[$key][$keys] = $poule[$key][$keys + 1];
                                    $poule[$key][$keys + 1] = $remp;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($poule as $key => $value) {
                foreach ($poule[$key] as $keys => $team) {
                    if($key < 2){
                        if($keys == 3){
                            // dd($poule[$key][$keys][0]);
                            $this->point($poule[$key][$keys][0], 5);
                        }elseif ($keys == 4) {
                            $this->point($poule[$key][$keys][0], 2);
                        }
                    }else{
                        if($keys == 2){
                            $this->point($poule[$key][$keys][0], 5);
                        }elseif ($keys == 3) {
                            $this->point($poule[$key][$keys][0], 2);
                        }
                    }
                }
            }

            $gameQuart[0] = $this->game($poule[0][2][0], $poule[1][0][0]); // 3eme poule A – 1er Poule B
            $gameQuart[1] = $this->game($poule[0][0][0], $poule[1][1][0]); // 1er Poule A – 2eme Poule B
            $gameQuart[2] = $this->game($poule[0][1][0], $poule[2][1][0]); // 2eme poule A – 2 eme Poule C
            $gameQuart[3] = $this->game($poule[1][2][0], $poule[2][0][0]); // 3eme Poule B – 1er poule C

            foreach ($gameQuart as $key => $value) {
                $this->point($value[6], 10);
            }

            foreach ($gameQuart as $key => $value) {
                if ($key <= 1) {
                    $demi[0][] = $value[5];
                } else {
                    $demi[1][] = $value[5];
                }
            }

            $gameDemi[0] = $this->game($demi[0][0], $demi[0][1]);
            $gameDemi[1] = $this->game($demi[1][0], $demi[1][1]);

            $this->point($gameDemi[0][6], 25);
            $this->point($gameDemi[1][6], 25);

            $final[0] = $gameDemi[0][5];
            $final[1] = $gameDemi[1][5];

            $gameFinal = $this->game($final[0], $final[1]);

            $this->point($gameFinal[6], 35);
            $this->point($gameFinal[5], 50);
        }

        return $this->render('competition/amerique.html.twig', [
            'chapeau' => $chapeau,
            'poule' => $poule,
            'gamePoule' => $gamePoule,
            'gameQuart' => $gameQuart,
            'gameDemi' => $gameDemi,
            'gameFinal' => $gameFinal
        ]);
    }
}
