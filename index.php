<?php
/*date_default_timezone_set('Europe/London');

$datetime = new DateTime('2008-08-03 12:35:23');
echo $datetime->format('Y-m-d H:i:s') . "\n"; 
$la_time = new DateTimeZone('America/Los_Angeles');
$datetime->setTimezone($la_time);
echo $datetime->format('Y-m-d H:i:s');


//Client ID:    2c363c79b69e4c7db2b8a379df6ef328
//Client Secret:    fa1492810d1044b59ed4f5466d808e41
//App Name: test_api_app
//Your access token:    6ceb9d53608e912ff110c118eb3c2e5c6a639a07*/

require 'ToDoist.php';

$var = new ToDoist();
$var->index();

?>