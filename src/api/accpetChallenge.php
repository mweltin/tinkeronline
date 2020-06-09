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


if( $has_permission->to('accept challenges') ){
  $accpet_challenge_query = <<<'SQL'
    INSERT INTO challenge (account_id, chapter_id)
    VALUES
    (?, ?)
SQL;
  $stmt = $pdo->prepare( $accpet_challenge_query );
  $stmt->execute([$tokenData['acct'], (int)$input['chapter_id'] ]);
  $response['accepted'] = true;
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

