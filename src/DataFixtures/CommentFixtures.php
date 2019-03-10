<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $file = fopen('public/data/comment.csv', "r");
        while(! feof($file))
        {
            $data = fgetcsv($file);
            if(empty($data))
                break;
            $comment = new Comment();
            
            $comment->setContents($data[1]);
            $comment->setAuthorUsername($data[2]);
            $comment->setAdded(new \DateTime($data[3]));
            $comment->setLikes($data[4]);
            $comment->setDislikes($data[5]);
            $comment->setParrentHash($data[6]);
            $comment->setHash($data[7]);
            $comment->setVideoHash($data[8]);
            
            $manager->persist($comment);
        }
        fclose($file);

        $manager->flush();
    }
}