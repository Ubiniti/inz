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
            $video->setHash($data[8]);
            $video->setTitle($data[1]);
            $video->setAuthorId($data[2]);
            $video->setUploaded(new \DateTime($data[3]));
            $video->setViews($data[4]);
            $video->setDescription($data[5]);
            $video->setDuration(new \DateTime($data[6]));
            $video->setCategory($data[7]);
            $video->setThumbsUp($data[8]);
            $video->setThumbsDown($data[9]);
            
            $manager->persist($video);
        }
        fclose($file);

        $manager->flush();
    }
}