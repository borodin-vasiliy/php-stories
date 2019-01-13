PHP Stories
==========

PHP-Library for create video-stories (MP4) using images, text and animations for them. Dependence of the GD-library.
This library create stories frame by frame and after that create video using ffmpeg. ffmpeg should be installed on your server!

Quick Start
-----------

Install the library using [composer](https://getcomposer.org):

    composer require borodin-vasiliy/php-stories

Examples
-----------

On examples dir you can found 2 samples how to use this library, below i will show how to create stories.

### Create object of the library

```php
<?php

use BorodinVasiliy\Stories;

$stories = new Stories([
        "width" => $stories_width,
        "height" => $stories_height,
        "duration" => $stories_duration
    ]);

?>
```

How you can see, args of library is array with some params. Which params you can use:

* "width" - width in px of stories layout
* "height" - height in px of stories layout
* "duration" - duration of stories in seconds
* "fps" - frame per second - how many frames will be created for each second of the video

All params are not required. If not set than, library will use default values 720x1280px, 5 seconds, 30 fps.

Now your future stories is ready for adding some elements to the video. In current moment you can add image, text, rectangle, ellipse with animation for them.

### Add objects to stories

Library has methods for adding each type of objects. Every time args of method - array of params.
All object-types has general params:

* "top" - position of image from top
* "left" - position of image from left
* "opacity" - like css opacity - opacity of element [0 .. 1]
* "rotate" - rotation of element [0 .. 359]
* "z-index" - like css z-index - at first on layer will be added elements with less z-index
* "start" - second, when element will be added to video
* "end" - second, when element should be removed from video

And objects has additional params for this type. Lets learn, how to add each type of object to video and which params we can use.

### Add image to stories

```php
<?php

$stories->addImage([
    "path" => __DIR__."image/cat.jpg",
    /*params*/
]);

?>
```

Additional params for images:

* "path" - required - required param with path and filename of image, ex. __DIR__."image/cat.jpg"
* "scale" - 1 default - scale of element, we dont have width and height params, just scale

### Add text to stories

```php
<?php

$stories->addText([
    "text" => "Hello world!",
    "path" => __DIR__."/fonts/helvetica.ttf",
    "size" => 130,
    /*other params*/
]);

?>
```

Additional params for text:

* "text" - required - Text, that you wanna add
* "path" - required - path to font .ttf file
* "size" - required - like css font-size
* "color" - like css color, ex "#ffffff"
* "width" - not required, if you use this param, text will be automatically splited to lines with setted width
* "align" - like css align [left, center, right]
* "shadow" - array of params, if you set them, this text will have shadow:
    * "color" - color of this shadow
    * "top" - offset from main text
    * "left" - offset from main text

### Add rectangle to stories

```php
<?php

$stories->addRectangle([
    "width" => 100,
    "height" => 100,
    /*other params*/
]);

?>
```

Additional params for rectangle:

* width - required - width of this rectangle
* height - required - height of this rectangle
* color - like css color of this rectangle

### Add ellipse to stories

```php
<?php

$stories->addEllipse([
    "width" => 100,
    "height" => 100,
    /*other params*/
]);

?>
```

Additional params for ellipse:

* width - required - width of this ellipse
* height - required - height of this ellipse
* color - like css color of this ellipse

Animation
-----------

Each object on video can be animated. Animation is change some param from start-value to animated value. Count of animations not limited. Each animation has own duration. Duration can be less then life-time of this element. When animation is done, animated param will be set like final point.

All types of objects has general params for animation:

* "start" - Second, when this animation should start
* "duration" - duration of animation (You can set or "duration", or "end" param)
* "end" - Second, when this animation should be finished
* "top" - To which position move this object
* "left" - To which position move this object
* "opacity" - To which opacity move this object
* "rotate" - Degree to rotate this element

And additional (just for this type):

* "scale" - for images
* "width" - for rectangle and ellipse
* "height" - for rectangle and ellipse

This library use Fluent Interface. That mean, you add element, after that can add animation for this element:

```php
<?php

$stories->addImage([
        // params
    ])->addAnimation([
        // params
    ]);

?>
```

Generate stories
-----------

When you add all elements to your stories and set animation, you ready for generate animation and result video.

```php
<?php

$file_hash = $stories->generate(__DIR__."/tmp");

?>
```

As arg of function - path to temporary dir, where will be saved frames and result video-file (MP4). As result of method generate() you will receive filename of result video in temporary dir. Generated frames will be automatically removed.

Sample of stories
-----------

<https://github.com/borodin-vasiliy/php-stories/tree/master/examples/stories/tmp/9b2a0d77f24779937e78f749329630ac.mp4>
