<?php

namespace App\DataFixtures;

use App\Entity\Continent;
use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $continent = new Continent;
        $continent->setName("europe");
        $manager -> persist($continent);

        for ($i = 0; $i < 50; $i++) {
            $team = new Team;
            $team->setName("equipe" . $i);
            $team->setScoring(rand(1, 1000));
            $team->setValue(rand(1, 100));
            $team->setMyTeam(false);
            $team->setContinent($continent);
            $manager -> persist($team);
        }



        $manager->flush();
    }
}
