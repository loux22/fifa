<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Promoter;
use App\Entity\Continent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EuroController extends AbstractController
{
    public function drawPromoter()
    {
        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Team::class);
        $teams = $repo->findBy(["continent" => 5]);
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
        }

        $lastYearTirage = $repoPromoter->findLastPromoterYear('euro');
        $yearsTirage = intval($lastYearTirage[0]['years']) + 4;

        $newPromoter = new Promoter($promoter);
        $newPromoter->setYears($yearsTirage);
        $newPromoter->setTournament("euro");
        $manager->persist($newPromoter);


        if ($otherPromoter != "") {
            $newOtherPromoter = new Promoter($otherPromoter);
            $newOtherPromoter->setYears($yearsTirage);
            $newOtherPromoter->setTournament("euro");
            $manager->persist($newOtherPromoter);
        }

        $manager->flush();
        return $yearsTirage;
    }

    /**
     * @Route("/euro", name="euro")
     */
    public function euro()
    {
        $repoPromoter = $this->getDoctrine()->getRepository(Promoter::class);
        $repo = $this->getDoctrine()->getRepository(Team::class);
        $repoEuro = $this->getDoctrine()->getRepository(Continent::class);
        $euro = $repoEuro->findBy([
            'name' => 'europe'
        ]);
        $promoter = []; // contient le/les pays organisateur
        $barrageOne = [];
        $barrageOne[0] = [];
        $barrageOne[1] = [];
        $barrageTwo = [];
        $barrageTwo[0] = [];

        $gameBarrageOne = [];
        $gameBarrageTwo = [];
        $rankTeam = [];
        $chapeau = [];
        $chapeau[0] = [];
        $chapeau[1] = [];
        $chapeau[2] = [];
        $chapeau[3] = [];

        $poule = [];
        if (isset($_POST['simulate'])) {
            $yearsTirage = $this->drawPromoter();
            $promoter = $repoPromoter -> findBy(['years' => $yearsTirage]);
            // $promoter = $repoPromoter->findBy(['years' => 2048]);
            $promoterId = [];
            foreach ($promoter as $key => $value) {
                $promoterId[$key] = $value->getTeam()->getId();
            }

            $teams = $repo->countryEuro($euro);
            $allTeams = [];
            foreach ($teams as $key => $value) {
                $allTeams[$key] = $value->getId();
            }
            $teams = array_diff($allTeams, $promoterId);
            foreach ($teams as $key => $team) {
                if (count($barrageOne[0]) <= 7) {
                    $barrageOne[0][] = $repo->find($team);
                } else if (count($barrageOne[1]) <= 7) {
                    $barrageOne[1][] = $repo->find($team);
                } else if (count($barrageTwo[0]) <= 11) {
                    $barrageTwo[0][] = $repo->find($team);
                } else {
                    $rankTeam[] = $repo->find($team);
                }
            }


            shuffle($barrageOne[0]);
            shuffle($barrageOne[1]);
            foreach ($barrageOne[0] as $key => $team) {
                $gameBarrageOne[$key] = $this->game($team, $barrageOne[1][$key]);
                $barrageTwo[1][] = $gameBarrageOne[$key][5];
                $this -> point($gameBarrageOne[$key][6], 2);
            }

            shuffle($barrageTwo[0]);
            shuffle($barrageTwo[1]);
            foreach ($barrageTwo[1] as $key => $team) {
                $gameBarrageTwo[$key] = $this->game($team, $barrageTwo[0][$key]);
                $this -> point($gameBarrageTwo[$key][6], 5);
                $rankTeam[] = $gameBarrageTwo[$key][5];
            }
            $gameBarrageTwo[8] = $this->game($barrageTwo[0][8], $barrageTwo[0][9]);
            $gameBarrageTwo[9] = $this->game($barrageTwo[0][10], $barrageTwo[0][11]);
            $rankTeam[] = $gameBarrageTwo[8][5];
            $this -> point($gameBarrageTwo[8][6], 5);
            $rankTeam[] = $gameBarrageTwo[9][5];
            $this -> point($gameBarrageTwo[9][6], 5);

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

            foreach ($promoter as $key => $value) {
                $chapeau[0][] = $value -> getTeam();
            }
            foreach ($rankTeam as $key => $team) {
                if (count($chapeau[0]) <= 3) {
                    $chapeau[0][] = $team;
                }else if(count($chapeau[1]) <= 3){
                    $chapeau[1][] = $team;
                }else if(count($chapeau[2]) <= 3){
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
                        $poule[$key][] = $chapeau[$keys][$key];
                }
            }
        }

        return $this->render('euro/euro.html.twig', [
            "promoter" => $promoter,
            "gameBarrageOne" => $gameBarrageOne,
            "barrageTwo" => $barrageTwo,
            "gameBarrageTwo" => $gameBarrageTwo,
            "rankTeam" => $rankTeam,
            "chapeau" => $chapeau,
            "poule" => $poule
        ]);
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
}
