<?php
namespace PHPizza\Updates;
use CzProject\GitPhp\Git as Git_;
class Git extends Git_
{
    public function open($directory)
	{
		return new GitRepository($directory, $this->runner);
	}
}
