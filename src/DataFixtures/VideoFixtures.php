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
            $video = new Video();
            $video->setTitle($data[1]);
            $video->setUploaded(new \DateTime($data[2]));
            $video->setViews($data[3]);
            $video->setDescription($data[4]);
            $video->setDuration(new \DateTime($data[5]));
            $video->setCategory($data[6]);
            //$video->setHash($data[7]);
            $video->setThumbsUp($data[8]);
            $video->setThumbsDown($data[9]);
            $video->setAuthorUsername($data[10]);
            $video->generateHash();
            
            $manager->persist($video);
        }
        fclose($file);

        $manager->flush();
    }
}