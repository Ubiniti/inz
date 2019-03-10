<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $file = fopen('public/data/video.csv', "r");
        while(! feof($file))
        {
            $data = fgetcsv($file);
            if(empty($data))
                break;
            $video = new Video();
            $video->setTitle($data[2]);
            $video->setAuthorUsername($data[3]);
            $video->setUploaded(new \DateTime($data[4]));
            $video->setViews($data[5]);
            $video->setDescription($data[6]);
            $video->setDuration(new \DateTime($data[7]));
            $video->setCategory($data[8]);
            $video->setThumbsUp($data[9]);
            $video->setThumbsDown($data[10]);
            $video->generateHash();
            
            $manager->persist($video);
        }
        fclose($file);

        $manager->flush();
    }
}