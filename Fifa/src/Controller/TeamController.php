<?php

namespace App\Controller;

use App\Entity\Continent;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->redirectToRoute('ranking');
    }


    /**
     * @Route("/ranking", name="ranking")
     */
    public function ranking(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Continent::class);
        $continent = $repo->findAll();
        $repository = $this->getDoctrine()->getRepository(Team::class);
        $team_ranking = $repository->ranking();


        $teamScoring = $request->request->all();
        if (count($teamScoring) > 1) {

            $count = 0;
            foreach ($teamScoring as $key => $value) {
                if ($count <= 49) {


                    $manager = $this->getDoctrine()->getManager();
                    $team = $manager->find(Team::class, $team_ranking[$count]->getId());

                    $newscore = intval($value);
                    $team->setScoring($team->getScoring() + $newscore);

                    $manager->persist($team);
                    $manager->flush();
                    $count += 1;
                }
            }
            return $this->redirectToRoute('ranking');
        }
        

        return $this->render('team/ranking.html.twig', [
            'team_ranking' => $team_ranking,
        ]);
    }
}
