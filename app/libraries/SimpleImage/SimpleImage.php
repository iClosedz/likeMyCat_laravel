<?php

//namespace ImageUpload;

class SimpleImage
{
    var $image;
    var $image_type;
    function load($filename)
    {
        $image_info       = getimagesize($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function fixRotation($orientation) {
        if($orientation > 0){
            $rotatedImage = imagerotate($this->image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[$orientation], 0);

            $temp = tmpfile();
            $tempFileName = stream_get_meta_data($temp)['uri'];
            if ($this->image_type == IMAGETYPE_JPEG) {
                imagejpeg($rotatedImage, $tempFileName, 100);
                $this->image = imagecreatefromjpeg($tempFileName);

            } elseif ($this->image_type == IMAGETYPE_GIF) {
                imagegif($rotatedImage, $tempFileName, 100);
                $this->image = imagecreatefromgif($tempFileName);

            } elseif ($this->image_type == IMAGETYPE_PNG) {
                imagepng($rotatedImage, $tempFileName, 100);
                $this->image = imagecreatefrompng($tempFileName);

            }

            imagedestroy($rotatedImage);
        }
    }

    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }
    function output($image_type = IMAGETYPE_JPEG)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }
    function getWidth()
    {
        return imagesx($this->image);
    }
    function getHeight()
    {
        return imagesy($this->image);
    }
    function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }
    function resizeToWidth($width)
    {
        $ratio  = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }
    function scale($scale)
    {
        $width  = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }
    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
}
?>