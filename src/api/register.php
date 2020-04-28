<?php
require __DIR__ . '/vendor/autoload.php';
require('mysql_connect.php');

use \Firebase\JWT\JWT;


$input = json_decode($HTTP_RAW_POST_DATA, true);

$stmt = $pdo->prepare("SELECT * FROM account WHERE username=?");
$stmt->execute([ $input['userName'] ]); 
$result = $stmt->fetchAll();

// test if username is already in use
// @todo have the angualr service do this on the fly 
if( sizeof($result) > 0 ){
    header('HTTP/1.1 410 User already exists');
    header('Content-type: application/json');
    print (  json_encode(['messsage' => 'user already exsits']) );
    exit();
}

$account_query = <<<'SQL'
INSERT INTO account 
( username, password, email )
VALUES
( ?, ? ,? )
SQL;

$stmt = $pdo->prepare( $account_query );
$stmt->execute([ $input['userName'], $input['password'], $input['email'] ]); 
$account_id = $pdo->lastInsertId();

$registrar_query = <<<'SQL'
INSERT INTO registrar 
( account_id )
VALUES
( ? )
SQL;

$stmt = $pdo->prepare( $registrar_query );
$stmt->execute([ $account_id ]); 
$registrar_id = $pdo->lastInsertId();


$key = "example_key";
$payload = array(
    "iss" => "http://example.org",
    "aud" => "http://example.com",
    "iat" => 1356999524,
    "nbf" => 1357000000
);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
$jwt = JWT::encode($payload, $key);


$add_token = <<<'SQL'
INSERT INTO token (token)
VALUES (?)
SQL;
$stmt = $pdo->prepare( $add_token );
$stmt->execute([ $jwt ]); 
$token_id = $pdo->lastInsertId();

$assocate_account_and_login_user = <<<'SQL'
UPDATE account 
SET 
registrar_id = ?, 
token_id = ?
WHERE 
account_id = ?
SQL;

$stmt = $pdo->prepare( $assocate_account_and_login_user );
$stmt->execute([ $registrar_id, $token_id, $account_id ]); 


$response = [
    'message' => 'new account created',
    'account_id' => $account_id,
    'registrar_id' => $registrar_id,
    'token' => $jwt
];

header('Content-type: application/json');
print (  json_encode($response) );


exit();