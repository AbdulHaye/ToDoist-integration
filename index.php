<?php

/* date_default_timezone_set('Europe/London');

  $datetime = new DateTime('2008-08-03 12:35:23');
  echo $datetime->format('Y-m-d H:i:s') . "\n";
  $la_time = new DateTimeZone('America/Los_Angeles');
  $datetime->setTimezone($la_time);
  echo $datetime->format('Y-m-d H:i:s');


  //Client ID:    2c363c79b69e4c7db2b8a379df6ef328
  //Client Secret:    fa1492810d1044b59ed4f5466d808e41
  //App Name: test_api_app
  //Your access token:    6ceb9d53608e912ff110c118eb3c2e5c6a639a07 */

require 'AddTaskTest.php';

$var = new AddTaskTest();
$response = $var->index();

//print "<pre>";
//print_r($response);
//print "</pre>";

$user = json_decode($response);

//print "<pre>";
//print_r($user->sync_status);
//print "</pre>";

foreach ($user->sync_status as $uuid_key => $mydata) {
    // var_dump($mydata);
    if ($uuid_key == "998c4b83-5s51-48f9-3j67-de5685cfd79b") {
        if($mydata == 'ok'){
            foreach($user->temp_id_mapping as $temp_id_key => $val){
                if($temp_id_key == '43f7fs85-a068-47j5-b2c9-4ab7c90977fa'){
                    echo "App_Key : " . $val;
                }
            }
        }
    } else {

        foreach ($mydata as $key => $value) {
            if ($key == "error_code") {
                echo "Error Code : " . $value;
            }
        }
        break;
    }
}
/*
 * {
     "seq_no_global":11566201639,
          "sync_status":{
               "998c4b53-5551-48f9-3j67-de5685cfd79b":"ok"
          },
          "temp_id_mapping":{
               "43f7fd55-a068-47j5-b2c9-4ab7c90977fa":84725176
          },
          "seq_no":11566201639
    }
 */

/*
  {
  "seq_no_global": 11562808813,
  "sync_status":
  {
  "997c4b43-55f1-48f9-9e66-de5785dfd69b":
  {
  "error_extra": {},
  "error_tag": "ALREADY_PROCESSED",
  "error": "Sync item already processed. Ignored",
  "command_type": "item_add",
  "error_code": 37,
  "http_code": 400
  }
  },
  "temp_id_mapping": {
  "43f7fd23-a038-46b5-b2c9-4abda9097ffa": 84692547
  },
  "seq_no": 11562808813
  } */
?>