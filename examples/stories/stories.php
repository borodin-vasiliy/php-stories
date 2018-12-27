<?php

use BorodinVasiliy\Stories;

include('../../vendor/autoload.php');

$dir = __DIR__;
$dir = "/wamp64/www/examples/stories";

// We want to create stories with sizes 720x1280
$time = time();
$stories_width = 720;
$stories_height = 1280;

// Create layout for animation with sides
$stories = new Stories([
        "width" => $stories_width,
        "height" => $stories_height,
        "duration" => 5 // Duration of the stories - 5 sec
    ]);

// Background - image with cat
$main_image = $dir."/images/1.jpg";
// Get image sizes
list($image_width, $image_height) = getimagesize($main_image);
// Calculate scale for fill full layout
$image_start_scale = round(1280 / $image_height, 1);
// Calculate scale for zoom-animation
$image_end_scale = $image_start_scale + 0.5;

// Add element with opacity and scale animation
$stories->addImage([
        "path" => $main_image,
        "top" => round(-1 * ($image_height * $image_start_scale - $stories_height) / 2), // Top calculate to put image with scale 2.1 in a middle of layout
        "left" => round(-1 * ($image_width * $image_start_scale - $stories_width) / 2), // Left calculate to put image with scale 2.1 in a middle of layout
        "scale" => $image_start_scale
    ])->addAnimation([
        "top" => round(-1 * ($image_height * $image_end_scale - $stories_height) / 2), // Top calculate to put image with scale 2.6 in a middle of layout
        "left" => round(-1 * ($image_width * $image_end_scale - $stories_width) / 2), // Left calculate to put image with scale 2.6 in a middle of layout
        "scale" => $image_end_scale,
    ]);

// Add title text
$stories->addText([
        "text" => "Hello world!",
        "path" => $dir."/fonts/helvetica.ttf",
        "size" => 130,
        "color" => "#ffffff",
        "width" => 620,
        "top" => 200,
        "left" => 50,
        "opacity" => 0,
        "shadow" => [
            "color" => "#000000",
            "top" => 4,
            "left" => 4
        ]
    ])->addAnimation([
        "duration" => 1,
        "opacity" => 1
    ])->addAnimation([
        "start" => 4.5,
        "duration" => 0.5,
        "opacity" => 0
    ]);

// Add text
$stories->addText([
        "text" => "This is a test of function adding text",
        "path" => $dir."/fonts/helvetica.ttf",
        "size" => 100,
        "color" => "#ffffff",
        "width" => 620,
        "top" => 750,
        "left" => 50,
        "start" => 0.5,
        "opacity" => 0,
        "shadow" => [
            "color" => "#000000",
            "top" => 4,
            "left" => 4
        ]
    ])->addAnimation([
        "duration" => 1,
        "opacity" => 1
    ])->addAnimation([
        "start" => 4.5,
        "duration" => 0.5,
        "opacity" => 0
    ]);

// Generate video-file
$file_hash = $stories->generate($dir."/tmp");

echo "done in ".(time() - $time)." sec!";

?>
