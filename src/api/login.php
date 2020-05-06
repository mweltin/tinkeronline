<?php
require 'header.php';

// grab the user input from the login from submission.
// @todo - validate and sanitaize inputs 
$input = json_decode($HTTP_RAW_POST_DATA, true);

// check submitted passwd against stored password
$pwdHash = hash($_PASSWD_HASH_ALGO, $input['password']);
$stmt = $pdo->prepare("SELECT password, account_id FROM account WHERE username=?");
$stmt->execute([ $input['userName'] ]); 
$result = $stmt->fetchAll();
$dbPasswdHash = hash($_PASSWD_HASH_ALGO, $result[0]['password']);

if($dbPasswdHash === $pwdHash){
    
    $tm = new tokenManager();
    $jwt = $tm->issueTokenToUser($result[0]['account_id']); 
   
    $response = [
        'message' => 'Welcome',
        'token' => $jwt,
        'passwd' => $dbPasswdHash,
        'pwd' > $pwdHash
    ];
    
    header('Authorzie: ' . $jwt);
    header('Content-type: application/json');
    print (  json_encode($response) );
} else {
    // picked up in angular as error.headers.get('message')
    header( 'message: ' . $result[0]['password']. '  ' . $input['password']);
    http_response_code (401);
}

exit();
