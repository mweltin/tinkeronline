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


// check for permissions to read chapters
$stmt = $pdo->prepare("SELECT * FROM account_action join action on action.action_id = account_action.action_id WHERE account_id=?");
$stmt->execute([ $tokenData['acct'] ]);
$result = $stmt->fetchAll();

if( $has_permission->to('view content') ){
  $cm = new chapterManager($pdo, $tokenData['acct']);
  $result = $cm->get_default_chapter();
  $response = [
    'chapter' => $result['chapter'],
    'title' => $result['title'],
    'chapter_id' => $result['chapter_id'],
    'accepted' => $result['accepted'],
    'solved' => $result['solved']
  ];
} else {
  header( 'message: permissioned denied to view content, please login.' );
  http_response_code (430);
  exit();
}

// issue new JWT
$jwt = $tm->issueTokenToUser($tokenData['acct']);
header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
