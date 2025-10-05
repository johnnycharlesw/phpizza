<?php
namespace PHPizza\Installer;
$scenePage = <<<HTML
<div class="center">
    <h1>Welcome!</h1>
    <p>PHPizza has been uploaded to the web server successfully if you are seeing this. Ready to install PHPizza?</p>
</div>
HTML;
$scenePage_=[
    "html" => $scenePage,
    "title" => "Welcome!"
];
return $scenePage_;