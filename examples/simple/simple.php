<?php

use BorodinVasiliy\Stories;

include('../../vendor/autoload.php');

// Create layout for animation
$stories = new Stories([
        "width" => 200,
        "height" => 400,
        "duration" => 2
    ]);

// Add element with opacity and scale animation
$stories->addImage([
        "src" => __DIR__."/images/3.png",
        "top" => 75,
        "left" => 100,
        "z-index" => 2,
        "start" => 0.5,
        "opacity" => 0,
        "scale" => 0.01,
        "animation" => [
                "top" => 50,
                "left" => 50,
                "duration" => 1,
                "scale" => 1,
                "opacity" => 100
            ]
    ]);
// Add first white background
$stories->addImage([
        "src" => __DIR__."/images/1.jpg",
        "top" => 0,
        "left" => 0,
        "z-index" => 0,
        "start" => 0,
        "end" => 1
    ]);
// Add second red background
$stories->addImage([
        "src" => __DIR__."/images/2.jpg",
        "top" => 0,
        "left" => 0,
        "z-index" => 1,
        "start" => 1
    ]);
// Generate frames with animation
$hash = $stories->generate(__DIR__."/tmp");

// Delete generated frame files
$stories->clear();

echo "done!";

?>
