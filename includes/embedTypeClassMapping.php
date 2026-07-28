<?php
namespace PHPizza;
$embedTypeClassMapping = [
    "youtube" => PizzadownEmbedHandlerYouTube::class,
    "mastodon" => PizzadownEmbedHandlerMastodon::class,
    'NoradSantaTracker' => PizzadownEmbedHandlerNoradSantaTracker::class,
    "facebook" => PizzadownEmbedHandlerFacebook::class,
    "webpage" => PizzadownEmbedHandlerWebPage::class,
    "video" => PizzadownEmbedHandlerVideo::class,
];