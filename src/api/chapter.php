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

// check for permissions to read chapters
$stmt = $pdo->prepare("SELECT * FROM account_action join action on action.action_id = account_action.action_id WHERE account_id=?");
$stmt->execute([ $tokenData['acct'] ]);
$result = $stmt->fetchAll();

if( array_search("view content", array_column($result, 'name')) !== FALSE ){
  $response = [
    'message' => 'here is some content from the chapter',
    'view content' =>  'allowed'
  ];
} else {
  $response = [
    'message' => $result,
    'view content' =>  'denied'
  ];
}

// look for most recent chapter that has been worked on
// if not chapters have been worked on return the latest chapter



// issue new JWT

$jwt = $tm->issueTokenToUser($registrar['account_id']);
header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
