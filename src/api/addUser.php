<?php
// composer auto loader
require 'header.php';

// this action requires a valid token
$tm = new tokenManager($pdo, constant('JWT_KEY'));

$header = apache_request_headers();

foreach ($header as $headers => $value) {
    error_log( "$headers: $value <br />\n") ;
}

// grab the user input from the registration.
$input = json_decode($HTTP_RAW_POST_DATA, true);

if( empty($input['userName']) ){
  header( 'Username can not be left empty' );
  http_response_code (427);
  exit();
}

$sanitize = new sanitize();
try{
  $sanitize->mustMatch($input['password'], $input['passwordConfirm']);
} catch( \Exception $e ) {
  header( 'message: '. $e->getMessage() );
  http_response_code (425);
  exit();
}

// test if the desired username already exists.
// @todo have the angualr service do this on the fly
$stmt = $pdo->prepare("SELECT * FROM account WHERE username=?");
$stmt->execute([ $input['userName'] ]);
$result = $stmt->fetchAll();
if( sizeof($result) > 0 ){
    header( 'message: User already exists');
    http_response_code (421);
    exit();
}

try {
    // create account
    $account_query = <<<'SQL'
    INSERT INTO account
    ( username, passwd )
    VALUES
    ( ?, ? )
SQL;

    $stmt = $pdo->prepare( $account_query );
    $stmt->execute([ $input['userName'], hash(constant('PASSWD_HASH_ALGO'), $input['password']) ]);
    $account_id = $pdo->lastInsertId();

    $assocate_account_and_login_user = <<<'SQL'
    UPDATE account
    SET
    registrar_id = ?
    WHERE
    account_id = ?
SQL;

    $stmt = $pdo->prepare( $assocate_account_and_login_user );
    $stmt->execute([ $registrar_id, $account_id ]);

} catch(Exception $e) {
    print (  json_encode($e) );
    http_response_code (444);
    exit();
}

$response = [
    'message' => 'new account created',
    'user' =>  $input['userName']
];

// issue new JWT

header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
