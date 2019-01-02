<?php

namespace BorodinVasiliy;

/**
 * @author Vasiliy Borodin
 * Email: mail@tltin.ru
 * Git: https://github.com/borodin-vasiliy/php-stories
 */

Class Stories {

    // Library variables
    private $frames = []; // Array of frames
    private $objects = []; // Array of all objects on video
    private $images = []; // Array of unique resources
    private $music = false; // Path to audio-file

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

        // Default params for future objects
        $this->default_params = [
                "top" => 0, // Position from top
                "left" => 0, // Position from left
                "opacity" => 1, // Opacity of element
                "rotate" => 0, // Rotate element degrees
                "start" => 0, // Second, when this element should be added to layout
                "end" => $this->duration, // Second, when this element should be removed from layout
                "animation" => []
            ];

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
        // Check input params
        if (!isset($args["path"])) {
            throw new \Exception("Param path is required. You can`t add image without path");
        }else{
            if (!is_file($args["path"])) {
                throw new \Exception("Image dont found on {$args['path']}");
            }
        }

        // Default values for image
        $defaults = array(
            "type" => "image",
            "scale" => 1, // Scale of image
	        "z-index" => count($this->objects) // Z-index of element
	    );

        // Merge default params and user params
        $params = array_merge($this->default_params, $args);
        $this->objects[] = array_merge($defaults, $params);

        return $this;
    }

    /**
    * Add text to animation
    * @param array $args - params for this text
    */
    public function addText(array $args = array()) {
        // Check input params
        if (!isset($args["text"])) {
            throw new \Exception("Param text is required. You can`t add text without text");
        }
        if (!isset($args["path"])) {
            throw new \Exception("Param path is required. You can`t add text without path to font");
        }else{
            if (!is_file($args["path"])) {
                throw new \Exception("Font dont found on {$args['path']}");
            }

            $ext = explode(".", strtolower($args["path"]));
            if ($ext[count($ext) - 1] != 'ttf') {
                throw new \Exception("Font should be on TTF-format");
            }
        }
        if (!isset($args["size"])) {
            throw new \Exception("Param size is required. You can`t add text without size");
        }

        $defaults = array(
            "type" => "text",
            "color" => "#ffffff", // Font color
            "align" => "left", // Aligment
	        "z-index" => count($this->objects) // Z-index of element
	    );

        // Merge default params and user params
        $params = array_merge($this->default_params, $args);
        $this->objects[] = array_merge($defaults, $params);

        return $this;
    }

    /**
    * Add rectangle to animation
    * @param array $args - params for this rectangle
    */
    public function addRectangle(array $args = array()) {
        // Check input params
        if (!isset($args["width"])) {
            throw new \Exception("Param width is required. You can`t add rectangle without width");
        }
        if (!isset($args["height"])) {
            throw new \Exception("Param height is required. You can`t add rectangle without height");
        }

        $defaults = array(
            "type" => "rectangle",
            "color" => "#ffffff", // Font color
            "width" => 0, // Rectangle width
            "height" => 0, // Rectangle height
            "z-index" => count($this->objects) // Z-index of element
        );

        // Merge default params and user params
        $params = array_merge($this->default_params, $args);
        $this->objects[] = array_merge($defaults, $params);

        return $this;
    }

    /**
    * Add ellipse to animation
    * @param array $args - params for this ellipse
    */
    public function addEllipse(array $args = array()) {
        // Check input params
        if (!isset($args["width"])) {
            throw new \Exception("Param width is required. You can`t add rectangle without ellipse");
        }
        if (!isset($args["height"])) {
            throw new \Exception("Param height is required. You can`t add rectangle without ellipse");
        }

        $defaults = array(
            "type" => "ellipse",
            "color" => "#ffffff", // Font color
            "width" => 0, // Ellipse width
            "height" => 0, // Ellipse height
            "z-index" => count($this->objects) // Z-index of element
        );

        // Merge default params and user params
        $params = array_merge($this->default_params, $args);
        $this->objects[] = array_merge($defaults, $params);

        return $this;
    }

    /**
    * Add music to video
    * @param string $path - path to audio-file
    */
    public function addMusic($path) {
        // Check input params
            if (!is_file($path)) {
            $this->music = $path;
        }else{
            throw new \Exception("Audio-file dont found on {$path}");
        }

        return $this;
    }

    /**
    * Add animation for last element
    * @param array $params - params of this element
    */
    public function addAnimation(array $params = array()) {
        if (!count($this->objects)) {
            throw new \Exception("Add something to layout before animate it");
        }
        $last_object_index = count($this->objects) - 1;
        $this->objects[$last_object_index]["animation"][] = $params;

        return $this;
    }

    /**
    * Add element to array of frames
    * @param array $params - params of this element
    * @param int $frame_start - frame, when this element should be added to stories
    * @param int $frame_end - frame, when this element should be removed from stories
    */
    private function addObject(array $params = array()) {
        // Calc when object should be added on frames
        $frame_start = $params["start"] * $this->fps;
        // And when removed
        $frame_end = $params["end"] * $this->fps - 1;
        // Unset this params, we doesnt need them anymore
        unset($params["start"]);
        unset($params["end"]);

        // Convert css-color to php
        if (isset($params["color"]) && $params["color"] != "transparent")
            $params["color"] = sscanf($params["color"], "#%02x%02x%02x");

        // Get image info
        if ($params["type"] == "image")
            list($image_width, $image_height, $image_type) = getimagesize($params["path"]);

        // Prepare animation start, duration and stop params
        $animations = [];
        foreach ($params["animation"] as $animation) {
            $animation["start"] = (!isset($animation["start"]) ? $frame_start : $animation["start"] * $this->fps);

            if (!isset($animation["duration"]))
                $animation["duration"] = ($frame_end - $frame_start) / $this->fps;

            $animation["stop"] = $animation["start"] + $animation["duration"] * $this->fps;

            $animations[] = $animation;
        }
        unset($params["animation"]);

        // Apply object-params to frames-array
        for ($i = $frame_start; $i <= $frame_end; $i++) {
            $tmpParam = array_merge([], $params);

            if ($params["type"] == "image") {
                $tmpParam["width"] = round($image_width * $params["scale"]);
                $tmpParam["height"] = round($image_height * $params["scale"]);
            }

            $this->frames[$i][] = $tmpParam;
        }

        // Calculate animations for this object
        foreach ($animations as $animation) {
            for ($i = $animation["start"]; $i <= $frame_end; $i++) {
                $last_object = count($this->frames[$i]) - 1;
                $tmpParam = $this->frames[$i][$last_object];

                if ($i == $animation["start"]) {
                    $animation = $this->getAnimation($tmpParam, $animation);
                    continue;
                }

                unset($this->frames[$i][$last_object]);
                if ($i <= $animation["stop"] && $i != $frame_end) {
                    // While element should be animated, change value of params
                    $tmpParam["top"] += round($animation["delta"]["top"] * ($i - $animation["start"]));
                    $tmpParam["left"] += round($animation["delta"]["left"] * ($i - $animation["start"]));
                    $tmpParam["opacity"] += $animation["delta"]["opacity"] * ($i - $animation["start"]);
                    $tmpParam["rotate"] += $animation["delta"]["rotate"] * ($i - $animation["start"]);

                    if (isset($tmpParam["scale"]))
                        $tmpParam["scale"] += $animation["delta"]["scale"] * ($i - $animation["start"]);

                    if ($params["type"] == "image") {
                        if (isset($tmpParam["width"]))
                            $tmpParam["width"] = round($image_width * $tmpParam["scale"]);

                        if (isset($tmpParam["height"]))
                            $tmpParam["height"] = round($image_height * $tmpParam["scale"]);
                    }

                    if ($params["type"] == "rectangle" || $params["type"] == "ellipse") {
                        $tmpParam["width"] += round($animation["delta"]["width"] * ($i - $animation["start"]));
                        $tmpParam["height"] += round($animation["delta"]["height"] * ($i - $animation["start"]));
                    }
                }else{
                    // If animation finished - change object params to final point of animation
                    if (isset($animation["top"]))
                        $tmpParam["top"] = $animation["top"];

                    if (isset($animation["left"]))
                        $tmpParam["left"] = $animation["left"];

                    if (isset($animation["opacity"]))
                        $tmpParam["opacity"] = $animation["opacity"];

                    if (isset($animation["rotate"]))
                        $tmpParam["rotate"] = $animation["rotate"];

                    if (isset($animation["scale"]))
                        $tmpParam["scale"] += $animation["delta"]["scale"] * ($animation["stop"] - $animation["start"]);

                    if ($params["type"] == "image") {
                        if (isset($tmpParam["width"]))
                            $tmpParam["width"] = round($image_width * $tmpParam["scale"]);

                        if (isset($tmpParam["height"]))
                            $tmpParam["height"] = round($image_height * $tmpParam["scale"]);
                    }

                    if ($params["type"] == "rectangle" || $params["type"] == "ellipse") {
                        $tmpParam["width"] = (isset($animation["width"]) ? $animation["width"] : $tmpParam["width"]);
                        $tmpParam["height"] = (isset($animation["height"]) ? $animation["height"] : $tmpParam["height"]);
                    }
                }

                $this->frames[$i][$last_object] = $tmpParam;
            }
        }
    }

    /**
    * Get delta params for animation
    * @param array $params - params of elements
    * @param array $animation - params of animation
    */
    private function getAnimation(array $params = array(), array $animation = array()) {
        $frame_start = $animation["start"];
        $frame_end = $animation["stop"];

        // Default delta values for animation
        $delta = [
            "top" => 0,
            "left" => 0,
            "opacity" => 0,
            "rotate" => 0,
            "scale" => 0,
            "width" => 0,
            "height" => 0
        ];

        // If this element has animation params, recalculate delta params for it
        if (isset($animation["top"])) {
            $delta["top"] = round(($animation["top"] - $params["top"]) / ($this->fps * $animation["duration"]), 2);
            $animation["top"] = $params["top"] + $delta["top"] * $this->fps * $animation["duration"];
        }

        if (isset($animation["left"])) {
            $delta["left"] = round(($animation["left"] - $params["left"]) / ($this->fps * $animation["duration"]), 2);
            $animation["left"] = $params["left"] + $delta["left"] * $this->fps * $animation["duration"];
        }

        if (isset($animation["opacity"])) {
            $delta["opacity"] = round(($animation["opacity"] - $params["opacity"]) / ($this->fps * $animation["duration"]), 4);
        }

        if (isset($animation["rotate"])) {
            $delta["rotate"] = round(($animation["rotate"] - $params["rotate"]) / ($this->fps * $animation["duration"]), 2);
        }

        if (isset($animation["scale"])) {
            $delta["scale"] = round(($animation["scale"] - $params["scale"]) / ($this->fps * $animation["duration"]), 4);
        }

        if (isset($animation["width"])) {
            $delta["width"] = round(($animation["width"] - $params["width"]) / ($this->fps * $animation["duration"]), 4);
        }

        if (isset($animation["height"])) {
            $delta["height"] = round(($animation["height"] - $params["height"]) / ($this->fps * $animation["duration"]), 4);
        }

        $animation["delta"] = $delta;

        return $animation;
    }

    /**
    * Wrap text if with is setted
    * @param int $fontSize - size of text
    * @param string $fontFace - path to font
    * @param string $string - text to wrap
    * @param int $width - width to wrap
    */
    private function wrapText($size, $path, $text, $width) {
        $ret = "";
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $words = explode(" ", $line);
            foreach ($words as $word) {
                $testboxWord = imagettfbbox($size, 0, $path, $word);

                $len = strlen($word);
                while ($testboxWord[2] > $width && $len > 0) {
                    $word = substr($word, 0, $len);
                    $len--;
                    $testboxWord = imagettfbbox($size, 0, $path, $word);
                }

                $teststring = $ret." ".$word;
                $testboxString = imagettfbbox($size, 0, $path, $teststring);
                if ($testboxString[2] > $width){
                    $ret .= ($ret == "" ? "" : "\n").$word;
                }else{
                    $ret .= ($ret == "" ? "" : " ").$word;
                }
            }
            $ret .= "\n";
        }

        return $ret;
    }

    /**
    * Add objects to array of frames
    */
    private function prepareObjects() {
        // Prepare objects if its neaded
        foreach ($this->objects as $o => $object) {
            if ($object["type"] == "text") {
                // Wrap text if it has width param
                if (isset($object["width"])) {
                    $object["text"] = $this->wrapText($object["size"], $object["path"], $object["text"], $object["width"]);
                }

                $lines = explode("\n", $object["text"]);

                $width = (isset($object["width"]) ? $object["width"] : $this->width);
                foreach ($lines as $ln => $line) {
                    if (!$line) continue;

                    // Add each not empty line as object
                    $tmpObject = array_merge([], $object);
                    $box = imagettfbbox($tmpObject["size"], 0, $tmpObject["path"], $line);
                    // If aligment != left, calculate how element should be moved
                    switch ($tmpObject["align"]) {
                        case "center":
                            $left = round(($width - ($box[2] - $box[0])) / 2);
                            break;
                        case "right":
                            $left = $width - ($box[2] - $box[0]);
                            break;
                        default:
                            $left = 0;
                            break;

                    }
                    $tmpObject["text"] = $line;
                    $tmpObject["left"] += $left;
                    $tmpObject["top"] += $ln * $tmpObject["size"];
                    foreach ($tmpObject["animation"] as $k => $animation) {
                        // Change animation params
                        if (isset($animation["left"]))
                            $tmpObject["animation"][$k]["left"] += $left;
                        if (isset($animation["top"]))
                            $tmpObject["animation"][$k]["top"] += $ln * $tmpObject["size"];
                    }

                    $this->addObject($tmpObject);
                }
            }else{
                $this->addObject($object);
            }
        }
    }

    /**
    * Generate animation frames and save them as files
    * @param string $dir - dir, where will be generated frames
    * @return string hash name of generated files
    */
    public function generate($dir) {
        // Calculate objects information and add it to frames array
        $this->prepareObjects();

        // Sort objects by z-index
        $this->orderObjectsByZIndex();

        $generated_frames = [];
        $hash = md5(time());
        foreach ($this->frames as $key => $objects) {
            // Path to generated frame
            $fn = "{$dir}/{$hash}-".sprintf('%07d', $key).".jpg";
            // Generate current frame and save it
            imagejpeg($this->generateFrame($objects), $fn, 100);
            // Save path to file
            $generated_frames[] = $fn;
        }

        // Now we can create video from frames using ffmpeg
        exec("ffmpeg -r {$this->fps} -y -i '{$dir}/{$hash}-%07d.jpg' '{$dir}/{$hash}.mp4'");
        if ($this->music) {
            exec("ffmpeg -i '{$dir}/{$hash}.mp4' -i '{$this->music}' '{$dir}/{$hash}_music.mp4'");
            unlink("{$dir}/{$hash}.mp4");
            copy("{$dir}/{$hash}_music.mp4", "{$dir}/{$hash}.mp4");
            unlink("{$dir}/{$hash}_music.mp4");
        }

        // Delete generated frame files
        //$this->clear($generated_frames);

        // Return result filename
        return "{$hash}.mp4";
    }

    /**
    * Delete all generated frame-files
    */
    private function clear($frames) {
        // Delete all generated frames
        foreach ($frames as $frame) {
            unlink($frame);
        }
    }

    /**
    * Get resource of image. If its new image - open it, if not - return from saved array
    * @param string link for file
    * @return resource image
    */
    private function getImageContent($path) {
        $hash = md5($path);
        if (!isset($this->images[$hash])) {
            // If its unique image - upload it
            $image_mime = image_type_to_mime_type(exif_imagetype($path));
            switch ($image_mime) {
                case "image/png":
                    $image = imagecreatefrompng($path);
                    break;
                case "image/jpeg":
                    $image = imagecreatefromjpeg($path);
                    break;
                case "default":
                    throw new \Exception("Can add just png or jpeg image-type");
            }

            // Add to array of used images
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

        foreach ($objects as $object) {
            // Add all objects to layout
            switch ($object["type"]) {
                case "image":
                    // Add image
                    $image = $this->generateImage($image, $object);
                    break;
                case "text":
                    // Add text
                    $image = $this->generateText($image, $object);
                    break;
                case "rectangle":
                    // Add rectangle
                    $image = $this->generateRectangle($image, $object);
                    break;
                case "ellipse":
                    // Add ellipse
                    $image = $this->generateEllipse($image, $object);
                    break;
            }
        }

        return $image;
    }

    /**
    * Add image to current frame
    * @param resource of current frame
    * @param array of object params
    * @return resource image
    */
    private function generateImage($image, $object) {
        $src = $this->getImageContent($object["path"]);
        $w_src = imagesx($src);
        $h_src = imagesy($src);
        $wr_src = $object["width"];
        $hr_src = $object["height"];

        // Create tmp layer
        $tmp = imagecreatetruecolor($wr_src, $hr_src);

        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $wr_src, $hr_src, $w_src, $h_src);

        $this->imagecopymerge_alpha($image, $tmp, $object["left"], $object["top"], 0, 0, $object["opacity"], $object["rotate"]);

        // Destroy temporary image
        imagedestroy($tmp);

        return $image;
    }

    /**
    * Add text to current frame
    * @param resource of current frame
    * @param array of object params
    * @return resource image
    */
    private function generateText($image, $object) {
        // Calculate text points
        $box = imagettfbbox($object["size"], 0, $object["path"], $object["text"]);
        $width = $box[0] + $box[2];
        $height = -1 * ($box[5] - $box[1]);
        $dx = $dy = 0;

        if (isset($object["shadow"])) {
            $dx = $object["shadow"]["left"];
            $dy = $object["shadow"]["top"];
            $width += $dx * 2;
            $height += $dy * 2;
        }

        // Create tmp layer
        $tmp = imagecreatetruecolor($width, $height);
        // Add transparent
        $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefill($tmp, 0, 0, $transparent);
        imagesavealpha($tmp, true);

        if (isset($object["shadow"])) {
            // Create color for text shadow
            $color = sscanf($object["shadow"]["color"], "#%02x%02x%02x");
            $font_color = imagecolorallocate($tmp, $color[0], $color[1], $color[2]);
            // Add text to image
            imagettftext($tmp, $object["size"], 0, $object["shadow"]["left"] - $box[0] + $dx, $object["shadow"]["top"] - $box[7] + $dy, $font_color, $object["path"], $object["text"]);
        }
        // Create color for text
        $font_color = imagecolorallocate($tmp, $object["color"][0], $object["color"][1], $object["color"][2]);
        imagettftext($tmp, $object["size"], 0, - $box[0] + $dx, - $box[7] + $dy, $font_color, $object["path"], $object["text"]);

        $this->imagecopymerge_alpha($image, $tmp, $object["left"], $object["top"], 0, 0, $object["opacity"], $object["rotate"]);

        // Destroy temporary image
        imagedestroy($tmp);

        return $image;
    }

    /**
    * Add rectangle to current frame
    * @param resource of current frame
    * @param array of object params
    * @return resource image
    */
    private function generateRectangle($image, $object) {
        $width = $object["width"];
        $height = $object["height"];
        $thickness = 0;

        if (isset($object["border"])) {
            $thickness = $object["border"]["thickness"];
            $width += 2 * $thickness;
            $height += 2 * $thickness;
        }

        // Create tmp layer
        $tmp = imagecreatetruecolor($width, $height);

        if (is_array($object["color"])) {
            // Create color for object
            $color = imagecolorallocate($tmp, $object["color"][0], $object["color"][1], $object["color"][2]);
            // Add object
            imagefilledrectangle($tmp, $thickness, $thickness, $thickness + $object["width"], $thickness + $object["height"], $color);
        }

        if (isset($object["border"])) {
            $object["border"]["color"] = sscanf($object["border"]["color"], "#%02x%02x%02x");
            $color = imagecolorallocate($tmp, $object["border"]["color"][0], $object["border"]["color"][1], $object["border"]["color"][2]);
            imagesetthickness($tmp, $thickness);
            imagerectangle($tmp, $thickness, $thickness, $thickness + $object["width"], $thickness + $object["height"], $color);
        }

        $this->imagecopymerge_alpha($image, $tmp, $object["left"] - $thickness, $object["top"] - $thickness, 0, 0, $object["opacity"], $object["rotate"]);

        // Destroy temporary image
        imagedestroy($tmp);

        return $image;
    }

    /**
    * Add ellipse to current frame
    * @param resource of current frame
    * @param array of object params
    * @return resource image
    */
    private function generateEllipse($image, $object) {
        $width = $object["width"];
        $height = $object["height"];
        $thickness = 0;

        if (isset($object["border"])) {
            $thickness = $object["border"]["thickness"];
            $width += 2 * $thickness;
            $height += 2 * $thickness;
        }

        // Create tmp layer
        $tmp = imagecreatetruecolor($width, $height);
        // Add transparent
        $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefill($tmp, 0, 0, $transparent);
        imagesavealpha($tmp, true);

        if (is_array($object["color"])) {
            // Create color for object
            $color = imagecolorallocate($tmp, $object["color"][0], $object["color"][1], $object["color"][2]);
            // Add object
            imagefilledellipse($tmp, round($object["width"] / 2) + $thickness, round($object["height"] / 2) + $thickness, $object["width"], $object["height"], $color);
        }

        if (isset($object["border"])) {
            $object["border"]["color"] = sscanf($object["border"]["color"], "#%02x%02x%02x");
            $color = imagecolorallocate($tmp, $object["border"]["color"][0], $object["border"]["color"][1], $object["border"]["color"][2]);
            imagesetthickness($tmp, $thickness);
            imagearc($tmp, round($object["width"] / 2) + $thickness, round($object["height"] / 2) + $thickness, $object["width"], $object["height"], 0, 360, $color);
        }

        $this->imagecopymerge_alpha($image, $tmp, $object["left"] - $thickness, $object["top"] - $thickness, 0, 0, $object["opacity"], $object["rotate"]);

        // Destroy temporary image
        imagedestroy($tmp);

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
    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $opacity = 1, $rotate = 0, $color = 0){
        $src_w = imagesx($src_im);
        $src_h = imagesy($src_im);
        if ($rotate) {
            $transparent = imagecolorallocatealpha($src_im, 0, 0, 0, 127);

            $src_im = imagerotate($src_im, $rotate, $transparent);
            $dx = imagesx($src_im) - $src_w;
            $dy = imagesy($src_im) - $src_h;
        }else{
            $dx = $dy = 0;
        }

        // Creating a cut resource
        $cut = imagecreatetruecolor($src_w + $dx, $src_h + $dy);
        // Copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x - round($dx / 2), $dst_y - round($dy / 2), $src_w + $dx, $src_h + $dy);
        // Copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w + $dx, $src_h + $dy);
        // Insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x - round($dx / 2), $dst_y - round($dy / 2), 0, 0, $src_w + $dx, $src_h + $dy, round($opacity * 100));
        // Destroy temporary image
        imagedestroy($cut);
    }
}

?>
