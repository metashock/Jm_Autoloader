<?php

// create a dummy class. (requires write access to 
// current working directory)
$class = <<<EOF
<?php
class Dummy {
    function hello() {
        echo __CLASS__,' was autoloaded', PHP_EOL;
    }
}
EOF;

file_put_contents('Dummy.php', $class);

// require Jm_Autoloader
require_once 'Jm/Autoloader.php';

$d = new Dummy();
$d->hello();
unlink('Dummy.php');


