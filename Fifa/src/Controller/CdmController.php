<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Promoter;
use App\Entity\Continent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CdmController extends AbstractController
{
    /**
     * @Route("/cdm", name="cdm")
     */
    public function cdm()
    {
        $repoPromoter = $this->getDoctrine()->getRepository(Promoter::class);
        $repo = $this->getDoctrine()->getRepository(Team::class);
        $repoContinent = $this->getDoctrine()->getRepository(Continent::class);
        $promoter = [];
        $barrageOceanie = [];
        $barrageOneAsie = [];
        $barrageTwoAsie = [];
        $barrageOneAfrique = [];
        $barrageTwoAfrique = [];
        $barrageAmerique = [];
        $barrageEurope1 = [];
        $barrageEurope2 = [];
        $rankTeam = [];
        $chapeau = [];
        $poule = [];
        $button = 1;
        if (isset($_POST['simulate'])) {
            $button = 2;
            $promoter = $this->displayPromoter($repoPromoter);
            $teamOceanie = $this->barrageOceanie($promoter, $repoContinent, $repo);
            if ($teamOceanie != null) {
                $rankTeam[] = $teamOceanie[0];
            }
            $barrageOceanie[] = $teamOceanie[1];

            $teamAsie = $this->barrageAsie($promoter, $repoContinent, $repo);
            if ($teamAsie != null) {
                $rankTeam[] = $teamAsie[0];
            }
            $barrageOneAsie[] = $teamAsie[1];
            $barrageTwoAsie[] = $teamAsie[2];

            $teamAfrique = $this->barrageAfrique($promoter, $repoContinent, $repo);
            foreach ($teamAfrique[1] as $key => $team) {
                $barrageOneAfrique[] = $team;
            }
            foreach ($teamAfrique[0] as $key => $team) {
                $rankTeam[] = $team;
            }
            if (isset($teamAfrique[2])) {
                foreach ($teamAfrique[2] as $key => $team) {
                    $barrageTwoAfrique[] = $team;
                }
            }

            $teamAmerique = $this->barrageAmerique($promoter, $repoContinent, $repo);
            $barrageAmerique = $teamAmerique[1];
            foreach ($teamAmerique[0] as $key => $team) {
                $rankTeam[] = $team;
            }
            foreach ($teamAmerique[2] as $key => $team) {
                $rankTeam[] = $team;
            }

            $teamEurope = $this->barrageEurope($promoter, $repoContinent, $repo);
            foreach ($teamEurope[1] as $key => $team) {
               $barrageEurope1[] = $team;
            }
            foreach ($teamEurope[2] as $key => $team) {
                $barrageEurope2[] = $team;
            }
            foreach ($teamEurope[0] as $key => $team) {
                $rankTeam[] = $team;
            }
            foreach ($teamEurope[3] as $key => $team) {
                $rankTeam[] = $team;
            }
            
            $tirage = $this -> tirage($promoter, $rankTeam);
            $rankTeam = $tirage[0];
            $chapeau = $tirage[1];
            $poule = $tirage[2];
        }

        return $this->render('cdm/index.html.twig', [
            "promoter" => $promoter,
            "barrageOceanie" => $barrageOceanie,
            "barrageOneAsie" => $barrageOneAsie,
            "barrageTwoAsie" => $barrageTwoAsie,
            "barrageOneAfrique" => $barrageOneAfrique,
            "barrageTwoAfrique" => $barrageTwoAfrique,
            "barrageAmerique" => $barrageAmerique,
            "barrageEurope1" => $barrageEurope1,
            "barrageEurope2" => $barrageEurope2,
            "rankTeam" => $rankTeam,
            "poule" => $poule,
            "chapeau" => $chapeau,
            "button" => $button
        ]);
    }

    public function tirage($promoter, $rankTeam){
        $chapeau = [];
        $chapeau[0] = [];
        $chapeau[1] = [];
        $chapeau[2] = [];
        $chapeau[3] = [];
        $poule = [];
        foreach ($rankTeam as $key => $rank) {
            foreach ($rankTeam as $keys => $value) {
                if (($keys + 1) < count($rankTeam)) {
                    if ($rankTeam[$keys + 1]->getScoring() > $rankTeam[$keys]->getScoring()) {
                        $remp = $rankTeam[$keys];
                        $rankTeam[$keys] = $rankTeam[$keys + 1];
                        $rankTeam[$keys + 1] = $remp;
                    }
                }
            }
        }
        foreach ($promoter as $key => $team) {
            $chapeau[0][] = $team -> getTeam();
        }
        foreach ($rankTeam as $key => $team) {
            if (count($chapeau[0]) < 8) {
                $chapeau[0][] = $team;
            }else if (count($chapeau[1]) < 8){
                $chapeau[1][] = $team;
            }else if (count($chapeau[2]) < 8){
                $chapeau[2][] = $team;
            }else{
                $chapeau[3][] = $team;
            }
        }
        shuffle($chapeau[0]);
        shuffle($chapeau[1]);
        shuffle($chapeau[2]);
        shuffle($chapeau[3]);
        foreach ($chapeau as $key => $c) {
            foreach ($c as $keys => $team) {
                $poule[$keys][] = $chapeau[$key][$keys];
            }
        }
        return [$rankTeam, $chapeau, $poule];
    }

    public function barrageEurope($promoter, $repoContinent, $repo)
    {
        $barrage = [];
        $barrage[0] = [];
        $barrage[1] = [];
        $barrage2[0] = [];
        $barrage2[1] = [];
        $office = [];
        $game1 = [];
        $game2 = [];
        $qualif = [];
        $allTeams = $this->teamWithoutPromoter($promoter, "europe", $repoContinent, $repo);
        foreach ($allTeams as $key => $team) {
            $teams[] = $team;
        }
        foreach ($teams as $key => $team) {
            if (count($barrage[0]) < 8){
                $barrage[0][] = $team;
            }else if (count($barrage[1]) < 8){
                $barrage[1][] = $team;
            }else if (count($barrage2[1]) < 12){
                $barrage2[1][] = $team;
            }else{
                $office[] = $team;
            }
        }
        shuffle($barrage[0]);
        shuffle($barrage[1]);
        foreach ($barrage[0] as $key => $team) {
            $game1[] = $this -> game($team, $barrage[1][$key]);
            $barrage2[0][] = $game1[$key][5];
        }
        shuffle($barrage2[0]);
        shuffle($barrage2[1]);
        foreach ($barrage2[0] as $key => $team) {
            $game2[] = $this -> game($team, $barrage2[1][$key]);
            $qualif[] = $game2[$key][5];
        }
        $game2[8] = $this->game($barrage2[1][8], $barrage2[1][9]);
        $game2[9] = $this->game($barrage2[1][10], $barrage2[1][11]);
        $qualif[] = $game2[8][5];
        $qualif[] = $game2[9][5];

        return [$qualif, $game1, $game2, $office];
    }

    public function barrageAmerique($promoter, $repoContinent, $repo)
    {
        $barrage = [];
        $barrage[0] = [];
        $barrage[1] = [];
        $office = [];
        $game = [];
        $qualif = [];
        $allTeams = $this->teamWithoutPromoter($promoter, "amerique", $repoContinent, $repo);
        foreach ($allTeams as $key => $team) {
            $teams[] = $team; 
        }
        foreach ($teams as $key => $team) {
            if (count($barrage[0]) < 4) {
                $barrage[0][] = $team;
            }else if (count($barrage[1]) < 4) {
                $barrage[1][] = $team;
            }
            else{
                $office[] = $team;
            }
        }
        shuffle($barrage[0]);
        shuffle($barrage[1]);
        foreach ($barrage[0] as $key => $team) {
            $game[] = $this -> game($team, $barrage[1][$key]);
            $qualif[] = $game[$key][5];
        }

        return [$qualif, $game, $office];
    }

    public function barrageAfrique($promoter, $repoContinent, $repo)
    {
        $demi = [];
        $final = [];
        $chapeau = [];
        $qualif = [];
        $teams = [];
        $allTeams = $this->teamWithoutPromoter($promoter, "afrique", $repoContinent, $repo);
        foreach ($allTeams as $key => $team) {
            $teams[] = $team;
        }
        if ($promoter[0]->getTeam()->getContinent()->getName() != "afrique") {
            foreach ($teams as $key => $team) {
                if ($key <= 3) {
                    $chapeau[0][] = $team;
                } else {
                    $chapeau[1][] = $team;
                }
            }
            shuffle($chapeau[0]);
            shuffle($chapeau[1]);
            foreach ($chapeau[0] as $key => $team) {
                $demi[] = $this->game($team, $chapeau[1][$key]);
                $qualif[]  = $demi[$key][5];
            }
            return [$qualif, $demi];
        }elseif (!isset($promoter[1]) && $promoter[0]->getTeam()->getContinent()->getName() == "afrique") {
            foreach ($teams as $key => $team) {
                if ($key <= 1) {
                    $chapeau[0][] = $team;
                }else {
                    $chapeau[1][] = $team;
                }
            }
            $demi[] = $this -> game($chapeau[0][0], $chapeau[0][1]);
            $chapeau[1][] = $demi[0][5];
            shuffle($chapeau[1]);
            foreach ($chapeau[1] as $key => $team) {
                if($key % 2 == 0){
                    $final[] = $this -> game($team, $chapeau[1][$key + 1]);
                }
            }
            foreach ($final as $key => $team) {
                $qualif[] = $team[5];
            }
            return [$qualif, $demi, $final];
        }else{
            foreach ($teams as $key => $team) {
                if ($key <= 3) {
                    $chapeau[0][] = $team;
                }else {
                    $chapeau[1][] = $team;
                }
            }
            shuffle($chapeau[0]);
            foreach ($chapeau[0] as $key => $team) {
                if($key % 2 == 0){
                    $demi[] = $this -> game($team, $chapeau[0][$key + 1]);
                }
            }
            $chapeau[1][] = $demi[0][5];
            $chapeau[1][] = $demi[1][5];
            shuffle($chapeau[1]);
            foreach ($chapeau[1] as $key => $team) {
                if($key % 2 == 0){
                    $final[] = $this -> game($team, $chapeau[1][$key + 1]);
                }
            }
            $qualif[] = $final[0][5];
            $qualif[] = $final[1][5];
            return [$qualif, $demi, $final];
        }
    }

    public function barrageAsie($promoter, $repoContinent, $repo)
    {
        $demi = [];
        $final = [];
        $chapeau = [];
        $teams = $this->teamWithoutPromoter($promoter, "asie", $repoContinent, $repo);
        foreach ($teams as $key => $team) {
            if ($key <= 1) {
                $chapeau[0][] = $team;
            } else {
                $chapeau[1][] = $team;
            }
        }
        shuffle($chapeau[0]);
        shuffle($chapeau[1]);
        if ($promoter[0]->getTeam()->getContinent()->getName() != "asie") {
            foreach ($chapeau[0] as $key => $team) {
                $demi[] = $this->game($team, $chapeau[1][$key]);
                $final[] = $demi[$key][5];
            }

            $game = $this->game($final[0], $final[1]);
            $team = $game[5];
            return [$team, $demi, $game];
        }
    }

    public function barrageOceanie($promoter, $repoContinent, $repo)
    {
        $teams = $this->teamWithoutPromoter($promoter, "oceanie", $repoContinent, $repo);
        if ($promoter[0]->getTeam()->getContinent()->getName() != "oceanie") {
            $game = $this->game($teams[0], $teams[1]);
            $team = $game[5];
            return [$team, $game];
        }
    }

    public function teamWithoutPromoter($promoter, $continent, $repoContinent, $repo)
    {
        $continent = $repoContinent->findBy([
            'name' => $continent
        ]);
        $promoterId = [];
        foreach ($promoter as $key => $value) {
            $promoterId[$key] = $value->getTeam()->getId();
        }
        $teams = $repo->countryEuro($continent);
        $allTeams = [];
        foreach ($teams as $key => $value) {
            $allTeams[$key] = $value->getId();
        }
        $teams = array_diff($allTeams, $promoterId);
        foreach ($teams as $key => $team) {
            $teams[$key] = $repo->find($team);
        }
        return $teams;
    }

    public function displayPromoter($repoPromoter)
    {
        $yearsTirage = $this->drawPromoter();
        $promoter = $repoPromoter->findBy(['years' => $yearsTirage]);
        return $promoter;
    }


    public function drawPromoter()
    {
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Team::class);
        $teams = $repo->findAll();
        $lastPromoter = [];
        $repoPromoter = $this->getDoctrine()->getRepository(Promoter::class);

        foreach ($teams as $key => $value) {
            $team = $repoPromoter->findOneBy(["team" => $value]);
            if ($team != []) {
                $lastPromoter[] = $team->getTeam()->getId();
            }
            $teams[$key] = $value->getId();
        }

        $nextPromoter = array_diff($teams, $lastPromoter);
        $allPromoter = [];
        foreach ($nextPromoter as $key => $id) {
            $allPromoter[] = $repo->findOneBy(["id" => $id]);
        }
        $randPromoter = rand(0, (count($allPromoter) - 1));
        $promoter = '';
        foreach ($allPromoter as $key => $team) {
            if ($key == $randPromoter) {
                $promoter = $repo->find($team);
            }
        }

        $otherPromoter = "";
        if ($promoter === null) {
            dd("toutes les équipes européene ont accueilli");
        }
        if ($promoter->getId() == 26) {
            $otherPromoter = $repo->find(["id" => 11]);
        } elseif ($promoter->getId() == 11) {
            $otherPromoter = $repo->find(["id" => 26]);
        } elseif ($promoter->getId() == 23) {
            $otherPromoter = $repo->find(["id" => 44]);
        } elseif ($promoter->getId() == 44) {
            $otherPromoter = $repo->find(["id" => 23]);
        } elseif ($promoter->getId() == 28) {
            $otherPromoter = $repo->find(["id" => 30]);
        } elseif ($promoter->getId() == 30) {
            $otherPromoter = $repo->find(["id" => 28]);
        } elseif ($promoter->getId() == 54) {
            $otherPromoter = $repo->find(["id" => 24]);
        } elseif ($promoter->getId() == 24) {
            $otherPromoter = $repo->find(["id" => 54]);
        } elseif ($promoter->getId() == 51) {
            $otherPromoter = $repo->find(["id" => 34]);
        } elseif ($promoter->getId() == 34) {
            $otherPromoter = $repo->find(["id" => 51]);
        } elseif ($promoter->getId() == 49) {
            $otherPromoter = $repo->find(["id" => 35]);
        } elseif ($promoter->getId() == 35) {
            $otherPromoter = $repo->find(["id" => 49]);
        } elseif ($promoter->getId() == 29) {
            $otherPromoter = $repo->find(["id" => 41]);
        } elseif ($promoter->getId() == 41) {
            $otherPromoter = $repo->find(["id" => 29]);
        } elseif ($promoter->getId() == 46) {
            $otherPromoter = $repo->find(["id" => 18]);
        } elseif ($promoter->getId() == 18) {
            $otherPromoter = $repo->find(["id" => 46]);
        } elseif ($promoter->getId() == 39) {
            $otherPromoter = $repo->find(["id" => 20]);
        } elseif ($promoter->getId() == 20) {
            $otherPromoter = $repo->find(["id" => 39]);
        } elseif ($promoter->getId() == 27) {
            $otherPromoter = $repo->find(["id" => 52]);
        } elseif ($promoter->getId() == 52) {
            $otherPromoter = $repo->find(["id" => 27]);
        } elseif ($promoter->getId() == 7) {
            $otherPromoter = $repo->find(["id" => 53]);
        } elseif ($promoter->getId() == 53) {
            $otherPromoter = $repo->find(["id" => 7]);
        } elseif ($promoter->getId() == 33) {
            $otherPromoter = $repo->find(["id" => 38]);
        } elseif ($promoter->getId() == 38) {
            $otherPromoter = $repo->find(["id" => 33]);
        } elseif ($promoter->getId() == 36) {
            $otherPromoter = $repo->find(["id" => 5]);
        } elseif ($promoter->getId() == 5) {
            $otherPromoter = $repo->find(["id" => 36]);
        } elseif ($promoter->getId() == 14) {
            $otherPromoter = $repo->find(["id" => 59]);
        } elseif ($promoter->getId() == 59) {
            $otherPromoter = $repo->find(["id" => 14]);
        } elseif ($promoter->getId() == 8) {
            $otherPromoter = $repo->find(["id" => 25]);
        } elseif ($promoter->getId() == 25) {
            $otherPromoter = $repo->find(["id" => 8]);
        } elseif ($promoter->getId() == 19) {
            $otherPromoter = $repo->find(["id" => 55]);
        } elseif ($promoter->getId() == 55) {
            $otherPromoter = $repo->find(["id" => 19]);
        } elseif ($promoter->getId() == 40) {
            $otherPromoter = $repo->find(["id" => 12]);
        } elseif ($promoter->getId() == 12) {
            $otherPromoter = $repo->find(["id" => 40]);
        } elseif ($promoter->getId() == 45) {
            $otherPromoter = $repo->find(["id" => 47]);
        } elseif ($promoter->getId() == 47) {
            $otherPromoter = $repo->find(["id" => 45]);
        } elseif ($promoter->getId() == 50) {
            $otherPromoter = $repo->find(["id" => 21]);
        } elseif ($promoter->getId() == 21) {
            $otherPromoter = $repo->find(["id" => 50]);
        } elseif ($promoter->getId() == 31) {
            $otherPromoter = $repo->find(["id" => 48]);
        } elseif ($promoter->getId() == 48) {
            $otherPromoter = $repo->find(["id" => 31]);
        }

        $lastYearTirage = $repoPromoter->findLastPromoterYear('cdm');
        $yearsTirage = intval($lastYearTirage[0]['years']) + 4;

        $newPromoter = new Promoter($promoter);
        $newPromoter->setYears($yearsTirage);
        $newPromoter->setTournament("cdm");
        $manager->persist($newPromoter);


        if ($otherPromoter != "") {
            $newOtherPromoter = new Promoter($otherPromoter);
            $newOtherPromoter->setYears($yearsTirage);
            $newOtherPromoter->setTournament("cdm");
            $manager->persist($newOtherPromoter);
        }

        $manager->flush();
        return $yearsTirage;
    }

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
}
