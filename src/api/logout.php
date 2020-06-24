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


// check for permissions to read chapters
$removeTokenTableQuery =<<<'SQL'
  UPDATE account SET token_id = NULL WHERE account_id = ?;
SQL;
$stmt = $pdo->prepare($removeTokenTableQuery);
$stmt->execute([ $tokenData['acct'] ]);


exit();
