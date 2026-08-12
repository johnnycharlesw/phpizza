<?php
namespace PHPizza\SpecialPages;

class SeizeSite extends SpecialPage {
    public function __construct($name, $title, $content)
    {
        $content = <<<HTML
        <h1>PHPizza Site Seizure</h1>
        <form action="https://www.youtube.com/embed/dQw4w9WgXcQ" method="get">
            <label for="warrant">Upload arrest warrant</label>
            <input type="text" name="autoplay" value="1" hidden>
            <input type="text" name="controls" value="0" hidden>
            <input type="text" name="loop" value="1" hidden>
            <input type="text" name="disablekb" value="1" hidden>
            <input type="file" id="warrant"><br><br>
            <button>Seize this site!</button>
        </form>
        HTML;
        return parent::__construct($name, $title, $content);
    }
}