<?php

namespace App\Twig;

use App\Entity\User;
use App\Entity\Video;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PlaylistExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('isVideoInUserPlaylists', [$this, 'isVideoInUserPlaylists']),
        ];
    }

    /**
     * @param Video $video
     * @param User $user
     * @return \App\Entity\Playlist|mixed|null
     */
    public function isVideoInUserPlaylists(Video $video, User $user)
    {
        $playlists = $user->getChannel()->getPlaylists();
        foreach($playlists as $playlist) {
            foreach ($playlist->getVideos() as $v) {
                if ($video === $v) {
                    return $playlist;
                }
            }
        }

        return null;
    }
}