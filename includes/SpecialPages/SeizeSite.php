<?php
namespace PHPizza\SpecialPages;

class SeizeSite extends SpecialPage {
    public function __construct($name, $title, $content)
    {
        $content = <<<HTML
        <h1>PHPizza Site Seizure</h1>
        <form action="https://www.youtube.com/watch" method="get">
            <input type="text" name="v" value="dQw4w9WgXcQ" hidden />
            <label for="warrant">Upload arrest warrant</label>
            <input type="file" id="warrant"><br><br>
            <button>Seize this site!</button>
        </form>
        HTML;
        return parent::__construct($name, $title, $content);
    }
}