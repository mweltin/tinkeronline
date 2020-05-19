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

$user_info_query = <<<'SQL'
    select * from account
    left join token on account.token_id = token.token_id
    left join registrar on registrar.account_id = account.account_id
    where account.account_id = ?
SQL;
$stmt = $pdo->prepare($user_info_query);
$stmt->execute([ $tokenData['acct'] ]);
$registrar = $stmt->fetch();

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
    $stmt->execute([ $registrar['registrar_id'], $account_id ]);

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

$jwt = $tm->issueTokenToUser($registrar['account_id']);
header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
