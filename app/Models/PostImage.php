<?php

namespace App\Models;


class PostImage
{

    protected $id = 0;

    public function __construct()
    {

        $this->default_image_width = 1920;
        $this->default_image_height = 1080;

    }

    public function getImageSizeUrl($filename, $width, $height)
    {
        $file = APP_DIR . 'public/img/posts/' . $width . 'x' . $height . '/' . $filename;

        if (file_exists($file)) {
            return DOMAIN . '/app/public/img/posts/' . $width . 'x' . $height . '/' . $filename;
        }
        // create image on the fly if it does not exists yet
        return $this->resize($filename, $width, $height);
    }

    public function getResizedImageUrl($filename, $width, $height)
    {
        $file = APP_DIR . 'public/img/posts/' . $width . 'x' . $height . '/' . $filename;

        if (file_exists($file)) {
            return DOMAIN . '/app/public/img/posts/' . $width . 'x' . $height . '/' . $filename;
        }
        // create image on the fly if it does not exists yet
        return $this->resizeCrop($filename, $width, $height, true);
    }

    private function resize($filename, $max_width, $max_height)
    {
        $folder = APP_DIR . 'public/img/posts/';
        $targetFolder = $folder . '/' . $max_width . 'x' . $max_height;
        $targetFile = $targetFolder . '/' . $filename;

        if (!is_dir($targetFolder)) {
            mkdir($targetFolder);
        }

        if (!file_exists($folder . $filename)) {
            return false;
        }

        list($orig_width, $orig_height) = getimagesize($folder . $filename);

        $width = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        # wider
        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }


        $image_p = imagecreatetruecolor($width, $height);

        if (false !== strpos($filename, '.png')) {
            $image = imagecreatefrompng($folder . $filename);
        } elseif (false !== strpos($filename, '.webp')) {
            $image = imagecreatefromwebp($folder . $filename);
        } else {
            $image = imagecreatefromjpeg($folder . $filename);
        }

        imagecopyresampled($image_p, $image, 0, 0, 0, 0,
            $width, $height, $orig_width, $orig_height);

        imagejpeg($image_p, $targetFile);

        return DOMAIN . '/app/public/img/posts/' . $max_width . 'x' . $max_height . '/' . $filename;
    }


    public function resizeCrop($filename, $width, $height, $crop = false)
    {

        $folder = APP_DIR . 'public/img/posts/';
        $src = $folder . $filename;

        $targetFolder = $folder . '/' . $width . 'x' . $height;
        $targetFile = $targetFolder . '/' . $filename;

        if (!is_dir($targetFolder)) {
            mkdir($targetFolder);
        }

        if (!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg') $type = 'jpg';
        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            default :
                return "Unsupported picture type!";
        }

        // resize

        $originalW = $w;
        $originalH = $h;

        if ($crop) {
            if ($w < $width or $h < $height) return "Picture is too small!";
            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        } else {
            if ($w < $width and $h < $height) return "Picture is too small!";
            $ratio = min($width / $w, $height / $h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        // imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
        imagecopyresampled($new, $img, 0, 0, ($originalW - $width) / 2, ($originalH - $height) / 2, $width, $height, $w, $h);


        var_dump($targetFile);

        switch ($type) {
            case 'bmp':
                imagewbmp($new, $targetFile);
                break;
            case 'gif':
                imagegif($new, $targetFile);
                break;
            case 'jpg':
                imagejpeg($new, $targetFile);
                break;
            case 'png':
                imagepng($new, $targetFile);
                break;
        }
        return $targetFile;
    }


    /**
     * @param $max_width
     * @param $max_height
     * @param $source_file
     * @param $dst_dir
     * @param $quality
     * @return false|void
     */
    public function ResizeCropImage($max_width, $max_height, $source_file, $dst_dir, $quality = 80)
    {
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ($width_new > $width) {
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);

        if ($dst_img) imagedestroy($dst_img);
        if ($src_img) imagedestroy($src_img);
    }
}