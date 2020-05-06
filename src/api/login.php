<?php
require 'header.php';

// grab the user input from the login from submission.
// @todo - validate and sanitaize inputs 
$input = json_decode($HTTP_RAW_POST_DATA, true);

// check submitted passwd against stored password
$pwdHash = hash(constant('PASSWD_HASH_ALGO'), $input['password']);
$stmt = $pdo->prepare("SELECT passwd, account_id FROM account WHERE username=?");
$stmt->execute([ $input['userName'] ]); 
$result = $stmt->fetchAll();
$dbPasswdHash = $result[0]['passwd'];

if($dbPasswdHash === $pwdHash){
    
    $tm = new tokenManager($pdo, constant('JWT_KEY'));
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
    header( 'message: Incorrect username or password');
    http_response_code (401);
}

exit();
