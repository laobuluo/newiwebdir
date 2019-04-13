<?php
class Captcha {
    private $width;
    private $height;
    private $codeNum;
    private $code;
    private $im;

    public function __construct($width = 80, $height = 20, $codeNum = 5) {
		session_start();
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
    }

    public function showImg() {
        //创建图片
        $this->createImg();
        //设置干扰元素
        $this->setDisturb();
        //设置验证码
        $this->setCaptcha();
        //输出图片
        $this->outputImg();
    }

    public function getCaptcha() {
        return $this->code;
    }

    private function createImg() {
        $this->im = imagecreatetruecolor($this->width, $this->height);
		
        $r = array(225, 255, 255, 223);
        $g = array(225, 236, 237, 255);
        $b = array(225, 236, 166, 125);
        $key = mt_rand(0, 3);
        $bgColor = imagecolorallocate($this->im, $r[$key], $g[$key], $b[$key]);
		imagefilledrectangle($this->im, 0, 0, $this->width - 1, $this->height - 1, $bgColor);
        imagerectangle($this->im, 0, 0, $this->width - 1, $this->height - 1, $bgColor);
    }

    private function setDisturb() {
        $area = ($this->width * $this->height) / 20;
        $disturbNum = ($area > 250) ? 250 : $area;
        //加入点干扰
        for ($i = 0; $i < $disturbNum; $i++) {
            $color = imagecolorallocate($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($this->im, mt_rand(1, $this->width - 2), mt_rand(1, $this->height - 2), $color);
        }
        //加入弧线
        for ($i = 0; $i <= 5; $i++) {
            $color = imagecolorallocate($this->im, mt_rand(128, 255), mt_rand(125, 255), mt_rand(100, 255));
            imagearc($this->im, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(30, 300), mt_rand(20, 200), 50, 30, $color);
        }
    }

    private function createCode() {
        $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";

        for ($i = 0; $i < $this->codeNum; $i++) {
            $this->code .= $str{mt_rand(0, strlen($str) - 1)};
        }
		$_SESSION['code'] = md5(strtolower($this->code));
    }

    private function setCaptcha() {
        $this->createCode();

        for ($i = 0; $i < $this->codeNum; $i++) {
            $color = imagecolorallocate($this->im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
            $size = mt_rand(floor($this->height / 5), floor($this->height / 3));
            $x = floor($this->width / $this->codeNum) * $i + 5;
            $y = mt_rand(0, $this->height - 20);
            imagechar($this->im, $size, $x, $y, $this->code{$i}, $color);
        }
    }

    private function outputImg() {
        if (imagetypes() & IMG_JPG) {
            header('Content-type:image/jpeg');
            imagejpeg($this->im);
        } elseif (imagetypes() & IMG_GIF) {
            header('Content-type: image/gif');
            imagegif($this->im);
        } elseif (imagetype() & IMG_PNG) {
            header('Content-type: image/png');
            imagepng($this->im);
        } else {
            die("Don't support image type!");
        }
    }
}

$captcha = new Captcha(80, 30, 5);
$captcha->showImg();
?>