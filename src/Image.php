<?php

namespace twCode;


use twCode\exception\PathNotFoundException;


class Image
{
    /**
     * 水印位置
     */
    const WATERMARK_POSITION = [
        'RIGHT_DOWN' => 1,
        'RIGHT_TOP'  => 2,
        'LEFT_DOWN'  => 3,
        'LEFT_TOP'   => 4,
    ];

    //默認水印位置
    private int $watermarkPosition = 1;
    //透明度
    private int $transparency = 50;

    //偏移量
    private int $margin = 10;

    //是否輸出
    private int $isShow = 0;

    //文字大小
    private int $fontSize = 20;

    //文字顏色
    private array $fontColor = [
        0,
        0,
        0,
    ];

    //角度
    private int $angle = 0;

    //字體文件
    private string $fontFilePath = __DIR__."/public/fonts/Dengb.ttf";


    /**
     * @param int $watermarkPosition
     */
    public function setWatermarkPosition( int $watermarkPosition ): void
    {
        $this->watermarkPosition = $watermarkPosition;
    }

    /**
     * @param int $transparency
     */
    public function setTransparency( int $transparency ): void
    {
        $this->transparency = $transparency;
    }

    /**
     * @param int $margin
     */
    public function setMargin( int $margin ): void
    {
        $this->margin = $margin;
    }

    /**
     * @param int $isShow
     */
    public function setIsShow( int $isShow ): void
    {
        $this->isShow = $isShow;
    }

    /**
     * @param array $logoSize
     */
    public function setLogoSize( array $logoSize ): void
    {
        $this->logoSize = $logoSize;
    }

    private array $logoSize = [
        'width'  => null,
        'height' => null,
    ];

    /**
     * 圖片水印
     * @param string $imagePath 水印圖片
     * @param string $logoPath  水印logo
     * @param string $savePath  保存文件名
     * @param array  $config    配置
     * @return bool
     */
    public function imageWatermark( string $imagePath, string $logoPath, string $savePath ):bool
    {
        if ( !file_exists( $imagePath ) ) {
            throw new PathNotFoundException();
        }
        if ( !file_exists( $logoPath ) ) {
            throw new PathNotFoundException( 'logo圖片未找到' );
        }
        $im = imagecreatefromstring( file_get_contents( $imagePath ) );
        $logo = imagecreatefromstring( file_get_contents( $logoPath ) );
        [
            $logoWidth,
            $logoHeight,
        ] = @getimagesize( $logoPath );

        $logoWidth = $this->logoSize[ 'width' ] ?? $logoWidth;
        $logoHeight = $this->logoSize[ 'height' ] ?? $logoHeight;

        [
            $imageWidth,
            $imageHeight,
            $type,
        ] = getimagesize( $imagePath );

        [
            $x,
            $y,
        ] = $this->getWatermarkPosition( [
            $imageWidth,
            $imageHeight,
        ], [
            $logoWidth,
            $logoHeight,
        ] );
        imagecopymerge( $im, $logo, $x, $y, 0, 0, $logoWidth, $logoWidth, $this->transparency );
        $this->exce( $im, $type, $savePath );
        imagedestroy( $logo );
        return true;
    }


    /**
     * 文字水印
     * @param string $imagePath
     * @param string $text
     * @param string $savePath
     * @return bool
     */
    public function textWatermark( string $imagePath, string $text, string $savePath ):bool
    {
        if ( !file_exists( $imagePath ) ) {
            throw new PathNotFoundException();
        }
        [
            $imageWidth,
            $imageHeight,
            $type,
        ] = getimagesize( $imagePath );
        $im = imagecreatefromstring( file_get_contents( $imagePath ) );
        $color = imagecolorallocate( $im, ...$this->fontColor );
        [
            $x,
            $y,
        ] = $this->getTextPosition( $text, $imageWidth, $imageHeight );
        imagettftext( $im, $this->fontSize, $this->angle, $x, $y, $color, $this->fontFilePath, $text );
        $this->exce( $im, $type, $savePath );
        return true;
    }

    /**
     * 計算文字文字
     * @param string $text
     * @param int    $imageWidth
     * @param int    $imageHeight
     * @return int[]
     */
    public function getTextPosition( string $text, int $imageWidth, int $imageHeight )
    {
        $x = 0;
        $y = 0;
        if ( $this->watermarkPosition === 1 ) {
            $x = $imageWidth - ( $this->fontSize * mb_strlen( $text ) ) - $this->margin;
            $y = $imageHeight - $this->fontSize - $this->margin;
        } elseif ( $this->watermarkPosition === 2 ) {
            $x = $imageWidth - ( $this->fontSize * mb_strlen( $text ) ) - $this->margin;
            $y = $this->margin;
        } elseif ( $this->watermarkPosition === 3 ) {
            $x = $this->margin;
            $y = $imageHeight - $this->fontSize - $this->margin;
        } else {
            $x = $this->margin;
            $y = $this->margin;
        }
        return [
            $x,
            $y,
        ];
    }

    /**
     * @param int $fontSize
     */
    public function setFontSize( int $fontSize ): void
    {
        $this->fontSize = $fontSize;
    }

    /**
     * @param array $fontColor
     */
    public function setFontColor( array $fontColor ): void
    {
        $this->fontColor = $fontColor;
    }

    /**
     * @param int $angle
     */
    public function setAngle( int $angle ): void
    {
        $this->angle = $angle;
    }

    /**
     * @param string $fontFilePath
     */
    public function setFontFilePath( string $fontFilePath ): void
    {
        $this->fontFilePath = $fontFilePath;
    }

    /**
     * 水印位置
     * @param int $position
     * @return array
     */
    protected function getWatermarkPosition( $image, $logo ): array
    {
        $x = 0;
        $y = 0;
        if ( $this->watermarkPosition === 1 ) {
            $x = $image[ '0' ] - $logo[ '0' ] - $this->margin;
            $y = $image[ '1' ] - $logo[ '1' ] - $this->margin;
        } elseif ( $this->watermarkPosition === 2 ) {
            $x = $image[ '0' ] - $logo[ '0' ] - $this->margin;
            $y = $this->margin;
        } elseif ( $this->watermarkPosition === 3 ) {
            $x = $this->margin;
            $y = $image[ '1' ] - $logo[ '1' ] - $this->margin;
        } else {
            $x = $this->margin;
            $y = $this->margin;
        }
        return [
            $x,
            $y,
        ];
    }

    /**
     * @param        $im
     * @param int    $type
     * @param string $savePath
     * @return void
     */
    private function exce( $im, int $type, string $savePath )
    {
        switch ( $type ) {
            case 1://GIF
                header( 'Content-Type: image/gif' );
                $this->isShow ? imagegif( $im ) : imagegif( $im, $savePath );
                break;
            case 2://JPG
                header( 'Content-Type: image/jpeg' );
                $this->isShow ? imagejpeg( $im ) : imagejpeg( $im, $savePath );
                break;
            case 3://PNG
                header( 'Content-Type: image/png' );
                $this->isShow ? imagepng( $im ) : imagepng( $im, $savePath );
                break;
            default:
                break;
        }
        imagedestroy( $im );
    }


}