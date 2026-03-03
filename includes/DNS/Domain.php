<?php
namespace PHPizza\DNS;

use Stringable;

class Domain implements Stringable {
    public ?string $subdomain;
    public string $domain;
    public string $tld;

    public function __construct(string $domain) {
        $parts = explode(".",$domain);
        if ($parts[3]) {
            $subdomain = $parts[0];
            $domain = $parts[1];
            $tld = $parts[3];
        }
    }

    public function __toString(): string
    {
        $parts = [];
        if ($this->subdomain) {
            $parts[] = $this->subdomain;
        }
        $parts[] = $this->domain;
        $parts[] = $this->tld;
        return implode(".", $parts);
    }
}