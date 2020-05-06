<?php
// composer auto loader
require __DIR__ . '/vendor/autoload.php';
// establish a mysql connection and assigns it to variable $pdo
require('mysql_connect.php');
// library to create JW tokens to be issued after a successful registration
use \Firebase\JWT\JWT;

// grab the user input from the registration.
// @todo - validate and sanitaize inputs 
$input = json_decode($HTTP_RAW_POST_DATA, true);

// test if the desired username already exists.  
// @todo have the angualr service do this on the fly 
$stmt = $pdo->prepare("SELECT * FROM account WHERE username=?");
$stmt->execute([ $input['userName'] ]); 
$result = $stmt->fetchAll();
if( sizeof($result) > 0 ){
    header('HTTP/1.1 410 User already exists');
    header('Content-type: application/json');
    print (  json_encode(['messsage' => 'user already exsits']) );
    exit();
}

// registration process (all performed in a DB transaction)
// 1) create account
// 2) enter registration information
// 3) generate and save JWT
// 4) link registrar, jwt to the new account
   
try {

    $pdo->beginTransaction();
    // create account 
    $account_query = <<<'SQL'
    INSERT INTO account 
    ( username, password, email )
    VALUES
    ( ?, ? ,? )
SQL;

    $stmt = $pdo->prepare( $account_query );
    $stmt->execute([ $input['userName'], hash($_PASSWD_HASH_ALGO, $input['password']), $input['email'] ]); 
    $account_id = $pdo->lastInsertId();
 
    // record registration information
    $registrar_query = <<<'SQL'
    INSERT INTO registrar 
    ( account_id )
    VALUES
    ( ? )
SQL;

    $stmt = $pdo->prepare( $registrar_query );
    $stmt->execute([ $account_id ]); 
    $registrar_id = $pdo->lastInsertId();



    $add_token = <<<'SQL'
    INSERT INTO token (token)
    VALUES (?)
SQL;

    $stmt = $pdo->prepare( $add_token );
    $stmt->execute([ $jwt ]); 
    $token_id = $pdo->lastInsertId();

    // Join JWT, account and registrar 
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

} catch(Exception $e) {
    $pdo->rollBack();
    header('HTTP/1.1 411 User creation error');
    header('Content-type: application/json');
    print (  json_encode(['messsage' => 'there was an error creating your user. please try again.']) );
    exit();
}

$pdo->commit();

$response = [
    'message' => 'new account created',
    'account_id' => $account_id,
    'registrar_id' => $registrar_id,
    'token' => $jwt
];

header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();