<?php

require('vendor/autoload.php');


$url = 'http://username:password@hostname:9090/path?arg=value#anchor';

echo "<pre>";
print_r(Uri::parseComponents($url));


