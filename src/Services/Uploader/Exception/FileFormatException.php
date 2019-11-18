<?php

namespace App\Services\Uploader\Exception;

class FileFormatException extends \Exception
{
    protected $message = 'Uploaded file format is not allowed';
}