<?php
namespace PHPizza;
class PizzadownEmbedHandlerNoradSantaTracker extends PizzadownEmbedHandler {
    public function __construct($value) {
        parent::construct("noradsantatracker", $value);
    }
    public function render() {
        global $enableSantaTrackerEmbeds;
        if (!$enableSantaTrackerEmbeds) {
            return "<!-- Embed not rendered because the site owner disabled santa tracker embeds -->";
        }
        $html = <<<html
<style>
    .noradtrackssanta-tracker{width: 680px; height: 515px;}
    .noradtrackssanta-countdown{width: 400px; height: 232px;}
</style>
html;
        if ($this->value == "santatracker") {
            $html .= <<<html
<iframe class="noradtrackssanta-tracker"
    src="https://www.noradsanta.org/embed.html" 
    allowfullscreen  
    title="NTS Tracking Map"></iframe>
html;
        } elseif ($this->value=="countdown") {
            $html .= <<html
<iframe class="noradtrackssanta-countdown"
    src="https://www.noradsanta.org/countdownClock.html" 
    title="NTS Countdown Clock"></iframe>
html;
        }
        return $html;
    }
}
