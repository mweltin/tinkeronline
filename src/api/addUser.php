<?php
// composer auto loader
require __DIR__ . '/vendor/autoload.php';
// establish a mysql connection and assigns it to variable $pdo
require('mysql_connect.php');


// grab the user input from the login from submission.
// @todo - validate and sanitaize inputs 
$input = json_decode($HTTP_RAW_POST_DATA, true);

// check submitted passwd against stored password
$pwdHash = hash($_PASSWD_HASH_ALGO, $input['password']);
$stmt = $pdo->prepare("SELECT password, id FROM account WHERE username=?");
$stmt->execute([ $input['userName'] ]); 
$result = $stmt->fetchAll();
$dbPasswdHash = hash($_PASSWD_HASH_ALGO, $result[0]['password']);
if($dbPasswdHash === $pwdHash){
    $payload = array(
        "iss" => "https://tinkercamp.org",
        "iat" => time(),
        "exp" => time() + (2 * 60 * 60),
        "acct" => $result[0]['account_id']
    );

    $jwt = JWT::encode($payload, $_JWT_KEY);

    $add_token = <<<'SQL'
    INSERT INTO token (token)
    VALUES (?)
    SQL;
    $stmt = $pdo->prepare( $add_token );
    $stmt->execute([ $jwt ]); 
    $token_id = $pdo->lastInsertId();
    
    $response = [
        'message' => 'Welcome',
        'token' => $jwt
    ];
    
    header('Authorzie: ' . $jwt);
    header('Content-type: application/json');
    print (  json_encode($response) );
} else {
    header('HTTP/1.1 421 invalid login');
    header('Content-type: application/json');
    print (  json_encode(['messsage' => 'User name or password were incorrect']) );
}

exit();
