<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GeochatMessagesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $messages = [
            [-0.573892, 44.8451032, 'Super la #foire place des quinconces!'],
            [-0.6116024, 44.7913014, 'Un bonjour du département #iutinformatique'],
            [-0.712858, 44.830072, "Je viens d'arriver à #aeoroportDeBordeaux, j'arrive en ville!"],
            [-0.5684104, 44.8942553, "Bientôt le #BordeauxGeekFest, venez nombreux!"],
            [-0.6020268, 44.8293753, "Prenez soin de vous et des autres, donnez votre sang"]
        ];

        foreach ($messages as list($lng, $lat, $text)) {
            $message = new Message;
            $message
                ->setText($text)
                ->setLongitude($lng)
                ->setLatitude($lat);
            $manager->persist($message);
        }

        $manager->flush();
    }
}
