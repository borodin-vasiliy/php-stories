<?php

namespace BorodinVasiliy\Stories;

/**
 * @author Vasiliy Borodin
 * Date: 17.11.2018
 * Git: https://github.com/borodin-vasiliy/php-stories
 */

Class Stories {

    private $data = [];
    private $frames = [];
    private $images = [];
    private $generated_frames = [];

    function __construct(array $args = array()) {
        // Default values for layout
        $defaults = array(
	        "width" => 720, // Width of layout
	        "height" => 1280, // Height of layout
	        "duration" => 5, // Duration of animation
	        "fps" => 30 // Frame per second
	    );

        // Merge default values with setted values and save it
        foreach (array_merge($defaults, $args) as $key => $value)
            $this->$key = $value;

        // Prepare empty frames
        $count_frames = $this->duration * $this->fps - 1;
        for ($i = 0; $i <= $count_frames; $i++)
            $this->frames[$i] = [];
    }

    /**
    * Add image to animation
    * @param array $args - params for this image
    */
    public function addImage(array $args = array()) {
        if (!isset($args["src"])) {
            throw new Exception("Param src is required. You can`t add image without src");
        }

        // Default values for image
        $defaults = array(
	        "top" => 0, // Position from top
            "left" => 0, // Position from left
            "opacity" => 1, // Opacity of image
            "scale" => 1, // Scale of image
	        "z-index" => 0, // Z-index of element
            "start" => 0, // Second, when this element should be added to layout
            "end" => $this->duration // Second, when this element should be removed from layout
	    );

        // Merge default params and user params
        $params = array_merge($defaults, $args);
        // Get image info
        list($image_width, $image_height, $image_type) = getimagesize($params["src"]);
        // Calc when element should added on frames
        $frame_start = $params["start"] * $this->fps;
        $frame_end = $params["end"] * $this->fps - 1;
        unset($params["start"]);
        unset($params["end"]);

        /*
            Element can be animated with params top, left, opacity, scale, duration.
            Duration - sec of animation. After thet element will be Yaf_Route_Static
        */

        // Default delta values for animation
        $dTop = 0;
        $dLeft = 0;
        $dOpacity = 0;
        $dScale = 0;
        $animationStopFrame = $frame_end;

        // If this element has animate params, recalculate delta params for it
        if (isset($params["animation"])) {
            if (!isset($params["animation"]["duration"])) {
                $params["animation"]["duration"] = ($frame_end - $frame_start) / $this->fps;
            }
            $animation = $params["animation"];

            if (isset($animation["top"])) {
                $dTop = round(($animation["top"] - $params["top"]) / ($this->fps * $animation["duration"]));
                $animation["top"] = $params["top"] + $dTop * $this->fps * $animation["duration"];
            }

            if (isset($animation["left"])) {
                $dLeft = round(($animation["left"] - $params["left"]) / ($this->fps * $animation["duration"]));
                $animation["left"] = $params["left"] + $dLeft * $this->fps * $animation["duration"];
            }

            if (isset($animation["opacity"]))
                $dOpacity = round(($animation["opacity"] - $params["opacity"]) / ($this->fps * $animation["duration"]), 4);

            if (isset($animation["scale"]))
                $dScale = round(($animation["scale"] - $params["scale"]) / ($this->fps * $animation["duration"]), 4);

            $animationStopFrame = $frame_start + $animation["duration"] * $this->fps;
            unset($params["animation"]);
        }

        // Add element to frames
        for ($i = $frame_start; $i <= $frame_end; $i++) {
            $tmpParam = array_merge([], $params);

            $tmpParam["width"] = round($image_width * $params["scale"]);
            $tmpParam["height"] = round($image_height * $params["scale"]);
            if ($i != $frame_start) {
                if ($i <= $animationStopFrame && $i != $frame_end) {
                    $tmpParam["top"] += round($dTop * ($i - $frame_start));
                    $tmpParam["left"] += round($dLeft * ($i - $frame_start));
                    $tmpParam["opacity"] += $dOpacity * ($i - $frame_start);
                    $tmpParam["scale"] += $dScale * ($i - $frame_start);
                    $tmpParam["width"] = round($image_width * $tmpParam["scale"]);
                    $tmpParam["height"] = round($image_height * $tmpParam["scale"]);
                }else{
                    if (isset($animation["top"]))
                        $tmpParam["top"] = $animation["top"];

                    if (isset($animation["left"]))
                        $tmpParam["left"] = $animation["left"];

                    if (isset($animation["opacity"]))
                        $tmpParam["opacity"] = $animation["opacity"];

                    if (isset($animation["scale"]))
                        $tmpParam["scale"] += $dScale * ($animationStopFrame - $frame_start);

                    $tmpParam["width"] = round($image_width * $tmpParam["scale"]);
                    $tmpParam["height"] = round($image_height * $tmpParam["scale"]);
                }
            }

            $this->frames[$i][] = $tmpParam;
        }
    }

    /**
    * Add text to animation
    * @param array $args - params for this image
    */
    public function addText(array $args = array()) {
        if (!isset($args["text"])) {
            throw new Exception("Param text is required. You can`t add text without text");
        }
        if (!isset($args["font"])) {
            throw new Exception("Param font is required. You can`t add text without font");
        }
        if (!isset($args["font-size"])) {
            throw new Exception("Param font-size is required. You can`t add text without font-size");
        }

        $defaults = array(
            "color" => [0, 0, 0], // Font color
	        "top" => 0, // Position from top
            "left" => 0, // Position from left
            "opacity" => 1, // Opacity of text
	        "z-index" => 0, // Z-index of element
            "start" => 0, // Second, when this element should be shown
            "end" => $this->duration // Second, when this element should be hidded
	    );

        // Merge default params and user params
        $params = array_merge($defaults, $args);
        // Calc when text should be added on frames
        $frame_start = $params["start"] * $this->fps;
        $frame_end = $params["end"] * $this->fps - 1;
        unset($params["start"]);
        unset($params["end"]);

        // If width is setted, try to split text to lines
        if (isset($params["width"])) {
            $symbol_width = $params["font-size"] * 0.4;
            $symbols_count = round($params["width"] / $symbol_width);
            $params["text"] = wordwrap($params["text"], $symbols_count, "\n", true);
        }

        /*
            Element can be animated with params top, left, opacity, scale, duration.
            Duration - sec of animation. After thet element will be Yaf_Route_Static
        */

        // Default delta values for animation
        $dTop = 0;
        $dLeft = 0;
        $dOpacity = 0;
        $animationStopFrame = $frame_end;

        // If this element has animate params, recalculate delta params for it
        if (isset($params["animation"])) {
            $animation = $params["animation"];
            if (!isset($params["animation"]["duration"]))
                throw new Exception("Param duration of animation is required");

            if (isset($animation["top"])) {
                $dTop = round(($animation["top"] - $params["top"]) / ($this->fps * $animation["duration"] - 1));
                $animation["top"] = $params["top"] + $dTop * $this->fps * $animation["duration"];
            }

            if (isset($animation["left"])) {
                $dLeft = round(($animation["left"] - $params["left"]) / ($this->fps * $animation["duration"] - 1));
                $animation["left"] = $params["left"] + $dLeft * $this->fps * $animation["duration"];
            }

            if (isset($animation["opacity"]))
                $dOpacity = round(($animation["opacity"] - $params["opacity"]) / ($this->fps * $animation["duration"] - 1), 4);

            $animationStopFrame = $frame_start + $animation["duration"] * $this->fps;
            unset($params["animation"]);
        }

        // Add element to frames
        for ($i = $frame_start; $i <= $frame_end; $i++) {
            $tmpParam = array_merge([], $params);

            if ($i != $frame_start) {
                if ($i < $animationStopFrame && $i != $frame_end) {
                    $tmpParam["top"] += round($dTop * ($i - $frame_start));
                    $tmpParam["left"] += round($dLeft * ($i - $frame_start));
                    $tmpParam["opacity"] += $dOpacity * ($i - $frame_start);
                }else{
                    if (isset($animation["top"]))
                        $tmpParam["top"] = $animation["top"];

                    if (isset($animation["left"]))
                        $tmpParam["left"] = $animation["left"];

                    if (isset($animation["opacity"]))
                        $tmpParam["opacity"] = $animation["opacity"];
                }
            }
            $this->frames[$i][] = $tmpParam;
        }
    }

    /**
    * Generate animation frames and save them as files
    * @param string $dir - dir, where will be generated frames
    * @return string hash name of generated files
    */
    public function generate($dir) {
        // Sort objects by z-index
        $this->orderObjectsByZIndex();

        $hash = md5(time());
        foreach ($this->frames as $key => $objects) {
            // Path to generated frame
            $fn = "{$dir}/{$hash}-".sprintf('%07d', $key).".jpg";
            // Generate current frame and save it
            imagejpeg($this->generateFrame($objects), $fn, 100);
            // Save path to file
            $this->generated_frames[] = $fn;
        }

        // Now we can create video from frames using ffmpeg
        exec("ffmpeg -r {$this->fps} -y -i '{$dir}/{$hash}-%07d.jpg' '{$dir}/{$hash}.mp4'");

        // Delete generated frame files
        $this->clear();

        // Return result filename
        return "{$hash}.mp4";
    }

    /**
    * Delete all generated frames files
    */
    public function clear() {
        // Delete all generated frames
        foreach ($this->generated_frames as $frame) {
            unlink($frame);
        }
        $this->generated_frames = [];
    }

    /**
    * Get resource of image. If its new image - open it, if not - return from saved array
    * @param string link for file
    * @return resource image
    */
    private function getImageContent($src) {
        $hash = md5($src);
        if (!isset($this->images[$hash])) {
            // If its unique image - upload it
            $image_mime = image_type_to_mime_type(exif_imagetype($src));
            switch ($image_mime) {
                case "image/png":
                    $image = imagecreatefrompng($src);
                    break;
                case "image/jpeg":
                    $image = imagecreatefromjpeg($src);
                    break;
                case "default":
                    throw new Exception("Can add just png or jpeg image-type");
            }
            // And add to array of used images
            $this->images[$hash] = $image;
        }

        return $this->images[$hash];
    }

    /**
    * Generate current frame
    * @param array of objects on this frame
    * @return resource image
    */
    private function generateFrame($objects) {
        // Create frame layout
        $image = imagecreatetruecolor($this->width, $this->height);
        // Add transparent
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        imagesavealpha($image, true);

        foreach ($objects as $object) {
            // Add all objects to layout
            if (isset($object["src"])) {
                // Add image
                $src = $this->getImageContent($object["src"]);
                $w_src = imagesx($src);
                $h_src = imagesy($src);
                $wr_src = $object["width"];
                $hr_src = $object["height"];

                // Create tmp layer
                $tmp = imagecreatetruecolor($wr_src, $hr_src);
                // Add transparent
                $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                imagefill($tmp, 0, 0, $transparent);
                imagesavealpha($tmp, true);

                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $wr_src, $hr_src, $w_src, $h_src);

                $this->imagecopymerge_alpha($image, $tmp, $object["left"], $object["top"], 0, 0, $wr_src, $hr_src, $object["opacity"]);
            }else{
                // Add text
                // Create layer for font
                $tmp = imagecreatetruecolor($this->width, $this->height);
                // Add transparent
                $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                imagefill($tmp, 0, 0, $transparent);
                imagesavealpha($tmp, true);

                $font_color = imagecolorallocate($tmp, $object["color"]["r"], $object["color"]["g"], $object["color"]["b"]);
                imagettftext($tmp, $object["font-size"], 0, $object["left"], $object["top"], $font_color, $object["font"], $object["text"]);

                $this->imagecopymerge_alpha($image, $tmp, 0, 0, 0, 0, $this->width, $this->height, $object["opacity"]);
            }
            // Destroy temporary image
            imagedestroy($tmp);
        }

        return $image;
    }

    /**
    * Order objects by z-index
    */
    private function orderObjectsByZIndex() {
        foreach ($this->frames as $key => $objects) {
            // Sort all objects in frame using usort with z-index
            usort($objects, function($a, $b){
                if ($a["z-index"] > $b["z-index"]) {
                    $res = 1;
                }else{
                    $res = -1;
                }
                return $res;
            });
            $this->frames[$key] = $objects;
        }
    }


    /**
    * Function of imagecopymerge with alpha
    */
    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        // Creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
        // Copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        // Copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        // Insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, round($pct * 100));
        // Destroy temporary image
        imagedestroy($cut);
    }

    function __set($key, $value) {
        $this->data[$key] = $value;
        return true;
    }

    function __get($key) {
        if (isset($this->data[$key]) == false) {
            throw new Exception("Param {$key} is not set, can`t get value");
			return null;
		}
		return $this->data[$key];
    }

}

?>
