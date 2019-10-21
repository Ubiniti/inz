<?php


namespace App\Services\Uploader\Exception;


class PathsJoinException extends \Exception
{
    protected $message = 'Cannot join paths from empty array';
}