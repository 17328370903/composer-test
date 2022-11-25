<?php

namespace twCode;

class Code
{

    private int $codeLen = 4;
    private string $codeString = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private int $width = 200;
    private int $height = 80;
    private string $fontPath;
    private int $fontSize = 30;
    private int $pixel = 120;
    private int $line = 10;
    private int $angle = 15;
    private string $key = 'tw_code';


    public function __construct()
    {
        $this->fontPath = __DIR__."/public/fonts/Dengb.ttf";
    }

    public function run():void
    {
        $code = $this->getRandCode();
        $im = imagecreate($this->width, $this->height);
        imagecolorallocatealpha($im, 255, 255, 255,0);
        $text_color = imagecolorallocate($im, 0, 0, 0);
        $step = ($this->width - $this->codeLen * $this->fontSize) / ($this->codeLen + 1);
        $space = $step;
        for ($i = 0; $i < $this->codeLen;$i++){
            imagettftext($im,$this->fontSize,$this->angle,$space,($this->height + $this->fontSize) / 2 ,$text_color,$this->fontPath,substr($code,$i,1));
            $space += $step + $this->fontSize;
        }
        $this->createPixel($im);
        $this->createLine($im);
        $this->saveCode($code);
        header ('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);
    }

    //获取随机的验证吗
    private function getRandCode(): string
    {
        $code = '';
        $len = strlen($this->codeString);
        for ($i = 0; $i < $this->codeLen; $i++) {
            $code .= substr($this->codeString, mt_rand(0, $len - 1), 1);
        }

        return $code;
    }

    //生成干扰点
    private function createPixel($im):void
    {
        for ($i = 0 ; $i < $this->pixel; $i++){
            $x = mt_rand(0,$this->width);
            $y = mt_rand(0,$this->height);
            $red = mt_rand(0,255);
            $blue = mt_rand(0,255);
            $green = mt_rand(0,255);
            $pixelColor = imagecolorallocate($im, $red, $green, $blue);
            imagesetpixel($im,$x,$y,$pixelColor);
        }
    }

    //生成干扰线
    private function createLine($im):void
    {
        for($i = 0; $i < $this->line; $i++){
            $x1 = mt_rand(0,$this->width);
            $y1 = mt_rand(0,$this->height);
            $x2 = mt_rand(0,$this->width);
            $y2 = mt_rand(0,$this->height);
            $red = mt_rand(0,255);
            $blue = mt_rand(0,255);
            $green = mt_rand(0,255);
            $lineColor = imagecolorallocate($im, $red, $green, $blue);
            imageline($im, $x1, $y1, $x2, $y2, $lineColor);
        }
    }

    //保存code到session
    private function saveCode(string $code):void
    {
        session_start();
        $_SESSION[$this->key] = $code;
    }

    //配置
    public function set(array $config):void
    {
        if(isset($config['codeLen']) && is_numeric($config['codeLen'])){
            $this->width = $config['codeLen'];
        }
        if(isset($config['width']) && is_numeric($config['width'])){
            $this->width = $config['width'];
        }
        if(isset($config['height']) && is_numeric($config['height'])){
            $this->height = $config['height'];
        }
        if(isset($config['fontSize']) && is_numeric($config['fontSize'])){
            $this->fontSize = $config['fontSize'];
        }
        if(isset($config['fontPath']) && !empty($config['fontPath'])){
            $this->fontPath = $config['fontPath'];
        }
        if(isset($config['pixel']) && is_numeric($config['pixel'])){
            $this->pixel = $config['pixel'];
        }
        if(isset($config['line']) && is_numeric($config['line'])){
            $this->line = $config['line'];
        }
        if(isset($config['angle']) && is_numeric($config['angle'])){
            $this->angle = $config['angle'];
        }

    }
    //验证
    public function verify(string $code):bool
    {
        session_start();
        if(($_SESSION[$this->key] ?? '') != $code){
            return false;
        }
        return true;
    }

}