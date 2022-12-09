<?php

namespace twCode\exception;

class PathNotFoundException extends \Exception
{
    protected  $code  = 404;
    protected  $message = '圖片未找到';

}