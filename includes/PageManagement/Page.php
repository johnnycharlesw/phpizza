<?php
namespace PHPizza\PageManagement;

use DateTime;
use PHPizza\Rendering\Pizzadown;

class Page {
    public int $status;
    public string $title;
    public string $markdown;
    private Pizzadown $pd;
    public DateTime $created_at;
    public DateTime $modified_at;

    public function __construct(int $status, string $title, string $markdown, DateTime $created_at, DateTime $modified_at) {
        $this->status=$status;
        $this->title=$title;
        $this->markdown=$markdown;
        $this->created_at=$created_at;
        $this->modified_at=$modified_at;
        $this->pd=new Pizzadown(true);
    }

    public function getHTML(){
        return $this->pd->text($this->markdown);
    }
}