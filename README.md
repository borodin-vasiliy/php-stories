PHP Stories
==========

Library for create stories using images, text and animations for them.
This library create stories frame by frame and after that create video using ffmpeg. ffmpeg should be installed on your server!

Quick Start
-----------

Install the library using [composer](https://getcomposer.org):

    composer require borodin-vasiliy/php-stories

Examples
-----------

On examples dir you can found 2 samples how to use this library, below i will show how to create stories.

Create object of the library
-----------

```php
<?php

use BorodinVasiliy\Stories;

$stories = new BorodinVasiliy\Stories\Stories([
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

All params are not required, if not set them, library will use default values 720x1280, 5 seconds, 30 fps.

Now your future stories is ready for adding some elements to the video. In current moment you can add images and text with animation for them.

Add images to stories
-----------

```php
<?php

$stories->addImage([
    "src" => __DIR__."image/cat.jpg",
    /*params*/
]);

?>
```

Again args is array with params:

* "src" - required param with path and filename of image, ex. __DIR__."image/cat.jpg"
* "top" - position of image from top
* "left" - position of image from left
* "z-index" - like css z-index - at first on layer will be added elements with less z-index
* "start" - second, when element will be added to video
* "end" - second, when element should be removed from video
* "opacity" - like css opacity - opacity of element [0 .. 1]
* "scale" - scale of element, we dont have width and height params, just scale
* "animation" - array of params for animation. This params is end point for same regular params.

Animation
-----------

Image and Text can be animated. Animation is move some param from regular value to animated value for animation duration time.
Duration can be less then life-time of this element. That mean, element can be animated for 1 second, but will be showed for the whole stories-time.
If some params not set in animation array, that mean this param is constant and not animated on result video.

Params for animation:

* "duration" - duration on animation
* "left" - position from left
* "top" - position from top
* "opacity" - opacity [0 .. 1]
* "scale" - scale of image

Default values for image:

* "top" = 0
* "left" = 0
* "opacity" = 1
* "scale" = 1
* "z-index" = 0
* "start" = 0 (on first frame of stories)
* "end" = calculated to not remove element from stories

Add text to stories
-----------

```php
<?php

$stories->addText([
    "text" => "Hello world!",
    /*other params*/
]);

?>
```

Again args is array with params:

* "text" - Text, that you wanna add
* "font" - path to font .ttf file
* "font-size" - like css font-size
* "color" - like css color
* "width" - not required, if you use it, text will be automatically splitet to lines with setted width
* "top" - position from top
* "left" - position from left
* "z-index" - like z-index
* "start" - second when text should be to stories
* "end" - second when text should be removed from stories
* "opacity" - like css opacity
* "animation" - array of params for animation, like with image. Params for animations, all same like for image (but text dont have scale):
    * "left"
    * "top"
    * "opacity"
    * "duration"

Default values:

* "top" = 0
* "left" = 0
* "opacity" = 1
* "start" = 0
* "end" = calculated, like image

Generate stories
-----------

When you add all elements to your stories and set animation, you ready for generate animation and result video.

```php
<?php

$file_hash = $stories->generate(__DIR__."/tmp");

?>
```

As arg of function - path to temporary dir, where will be saved frames and result video-file. As result of method generate() you will receive filename of result video in temporary dir. Generated frames will be automatically removed.
