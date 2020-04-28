<?php

$array = json_decode($HTTP_RAW_POST_DATA);
header('Content-type: application/json');
print (  json_encode($array) );
exit();