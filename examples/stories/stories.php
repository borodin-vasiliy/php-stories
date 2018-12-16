<?php

use BorodinVasiliy\Stories;

include('../../vendor/autoload.php');

// We want to create stories with sizes 720x1280
$time = time();
$stories_width = 720;
$stories_height = 1280;

// Create layout for animation with sides
$stories = new BorodinVasiliy\Stories\Stories([
        "width" => $stories_width,
        "height" => $stories_height,
        "duration" => 5 // Duration of the stories - 5 sec
    ]);

// Background - image with cat
$main_image = __DIR__."/images/1.jpg";
// Get image sizes
list($image_width, $image_height) = getimagesize($main_image);
// Calculate scale for fill full layout
$image_start_scale = round(1280 / $image_height, 1);
// Calculate scale for zoom-animation
$image_end_scale = $image_start_scale + 0.5;

// Add element with opacity and scale animation
$stories->addImage([
        "src" => $main_image,
        "top" => round(-1 * ($image_height * $image_start_scale - $stories_height) / 2), // Top calculate to put image with scale 2.1 in a middle of layout
        "left" => round(-1 * ($image_width * $image_start_scale - $stories_width) / 2), // Left calculate to put image with scale 2.1 in a middle of layout
        "z-index" => 1,
        "start" => 0,
        "opacity" => 1,
        "scale" => $image_start_scale,
        "animation" => [
                "top" => round(-1 * ($image_height * $image_end_scale - $stories_height) / 2), // Top calculate to put image with scale 2.6 in a middle of layout
                "left" => round(-1 * ($image_width * $image_end_scale - $stories_width) / 2), // Left calculate to put image with scale 2.6 in a middle of layout
                "scale" => $image_end_scale,
            ]
    ]);

// Add title text
$stories->addText([
        "text" => "Hello world!",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 130,
        "color" => [
                "r" => 255,
                "g" => 255,
                "b" => 255
            ],
        "width" => 620,
        "top" => 200,
        "left" => 50,
        "z-index" => 3,
        "start" => 0,
        "end" => 4.5,
        "opacity" => 0,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 1
            ]
    ]);
// Add shadow for title
$stories->addText([
        "text" => "Hello world!",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 130,
        "color" => [
                "r" => 0,
                "g" => 0,
                "b" => 0
            ],
        "width" => 620,
        "top" => 204,
        "left" => 54,
        "z-index" => 2,
        "start" => 0,
        "end" => 4.5,
        "opacity" => 0,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 1
            ]
    ]);

// Add title text with hide animation
$stories->addText([
        "text" => "Hello world!",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 130,
        "color" => [
                "r" => 255,
                "g" => 255,
                "b" => 255
            ],
        "width" => 620,
        "top" => 200,
        "left" => 50,
        "z-index" => 3,
        "start" => 4.5,
        "opacity" => 1,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 0
            ]
    ]);
// Add shadow for title with hide
$stories->addText([
        "text" => "Hello world!",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 130,
        "color" => [
                "r" => 0,
                "g" => 0,
                "b" => 0
            ],
        "width" => 620,
        "top" => 204,
        "left" => 54,
        "z-index" => 2,
        "start" => 4.5,
        "opacity" => 1,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 0
            ]
    ]);

// Add text
$stories->addText([
        "text" => "This is a test of function adding text",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 100,
        "color" => [
                "r" => 255,
                "g" => 255,
                "b" => 255
            ],
        "width" => 620,
        "top" => 750,
        "left" => 50,
        "z-index" => 5,
        "start" => 0.5,
        "end" => 4.5,
        "opacity" => 0,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 1
            ]
    ]);
// Add text-shadow
$stories->addText([
        "text" => "This is a test of function adding text",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 100,
        "color" => [
                "r" => 0,
                "g" => 0,
                "b" => 0
            ],
        "width" => 620,
        "top" => 754,
        "left" => 54,
        "z-index" => 4,
        "start" => 0.5,
        "end" => 4.5,
        "opacity" => 0,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 1
            ]
    ]);

// Add text with hide animation
$stories->addText([
        "text" => "This is a test of function adding text",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 100,
        "color" => [
                "r" => 255,
                "g" => 255,
                "b" => 255
            ],
        "width" => 620,
        "top" => 750,
        "left" => 50,
        "z-index" => 5,
        "start" => 4.5,
        "opacity" => 1,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 0
            ]
    ]);
// Add text-shadow
$stories->addText([
        "text" => "This is a test of function adding text",
        "font" => __DIR__."/fonts/helvetica.ttf",
        "font-size" => 100,
        "color" => [
                "r" => 0,
                "g" => 0,
                "b" => 0
            ],
        "width" => 620,
        "top" => 754,
        "left" => 54,
        "z-index" => 4,
        "start" => 4.5,
        "opacity" => 1,
        "animation" => [
                "duration" => 0.5,
                "opacity" => 0
            ]
    ]);

// Generate video-file
$file_hash = $stories->generate(__DIR__."/tmp");

echo "done in ".(time() - $time)." sec!";

?>
