<?php
require 'ToDoist.php';

$var = new ToDoist();
$response = $var->index();

print "<pre>";
print_r($response);
print "</pre>";

?>