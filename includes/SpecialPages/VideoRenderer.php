<?php
namespace PHPizza\SpecialPages;

use Override;

class VideoRenderer extends SpecialPage {
  public function __construct($name, $title, $content)
  {
    return parent::__construct('VideoRenderer', $title, '');
  }
  
  #[Override]
  public function getContent()
  {
        $videoID=htmlspecialchars($_GET['v'],ENT_QUOTES);
        header("Content-Security-Policy: frame-src 'self';");
        return <<<HTML
<script src="/assets/phpizza-client-scripts/videoplayer.js"></script>
<video id="video">
    <source src="/{$videoID}" type="video/mp4">
    Your browser does not support HTML video.
</video>
<br>
<div id="controls">
    <button onclick="window.playPause()" id="playPauseBtn"><img src="/node_modules/feather-icons/dist/icons/play.svg" class="icon" id="playIcon" /></button> 
    <input id="seek" type="range" min="0" step="0.01" value="0">
    <span id="timeStatus">0:00 / 0:00</span>
</div>
<link rel="stylesheet" href="/load.php?t=css&f=phpizza-css/videoplayer.css">
HTML;
  }
}