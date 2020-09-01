<?php

// composer auto loader
require 'header.php';

//grab http headers
$headers = apache_request_headers();

// grab the user input from the registration.
$input = json_decode($HTTP_RAW_POST_DATA, true);
$token = $headers['Authorzie'];

// this action requires a valid token
$tm = new tokenManager($pdo, constant('JWT_KEY'));
try{
  $tm-> verifyToken( $token );
} catch( \Exception $e ) {
  error_log( $e->getMessage() );
  header( 'message: Token verification failure. Please login again.' );
  http_response_code (429);
  exit();
}

$tokenData = $tm->parseToken($token);
$has_permission = new hasPermission($pdo, $tokenData['acct']);



if( $has_permission->to('upload assets') ){
    try {
    
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
            
        $folderPath = "/home/tinkerblake/solution_uploads/";
       
        $file_tmp = $_FILES['file']['tmp_name'];
        error_log("got here ". $file_tmp);

        $file_ext = strtolower(end(explode('.',$_FILES['file']['name'])));
        $file = $folderPath . uniqid() . '.'.$file_ext;
        move_uploaded_file($file_tmp, $file);
        $response['message'] = "all is good";
    } catch (\Exception $e) {

        error_log( $e->getMessage() );
        $response['message'] = "all is not good";

    }
}

// issue new JWT
$jwt = $tm->issueTokenToUser($tokenData['acct']);
header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
?>