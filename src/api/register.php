<?php
// composer auto loader
require 'header.php';

// grab the user input from the registration.
// @todo - validate and sanitaize inputs 
$input = json_decode($HTTP_RAW_POST_DATA, true);

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

// registration process (all performed in a DB transaction)
// 1) create account
// 2) enter registration information
// 3) generate and save JWT
// 4) link registrar, jwt to the new account
   
try {
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
    try{
        $tm = new tokenManager();
        $jwt = $tm->issueTokenToUser($account_id, $pdo); 
    } catch(Exception $e){
        throw $e;
    }
    
    // Join JWT, account and registrar 
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
   //  header( 'message: ' . $e->getMessage() );
   // http_response_code (444);
    exit();
}

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