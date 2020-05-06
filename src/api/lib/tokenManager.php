<?php
// library to create JW tokens to be issued after a successful registration
use \Firebase\JWT\JWT;
require('setting.php');

class tokenManager {

    private $key;

    function __construct() {
        include('setting.php');
        $key = $_JWT_KEY;
        error_log("key" . $key, 3, '../error_log' );
        
    }
    
    function issueTokenToUser($account_id, $pdo){
        // create JWT token 
        $payload = array(
            "iss" => "https://tinkercamp.org",
            "iat" => time(),
            "exp" => time() + (2 * 60 * 60),
            "acct" => $account_id
        );

        $jwt = JWT::encode($payload, constant('JWT_KEY'));

        $add_token = <<<'SQL'
            INSERT INTO token (token)
            VALUES (?)
SQL;
        $stmt = $pdo->prepare( $add_token );
        $stmt->execute([ $jwt ]); 
        $token_id = $pdo->lastInsertId();

        $assign_token_to_user = <<< 'SQL'
            UPDATE account 
                SET token_id = ? 
            WHERE
                account_id = ? 
SQL;
        $stmt->execute([ $assign_token_to_user ]); 

        return $jwt;
    }

    /**
     * Verify the token is valid
     * Check the database for the last token issued to the user and 
     * ensure the payloads match and the token hasn't expired.
     */
    function verifyToken( $token ){
        $verified = false; 
        
        $decoded = JWT::decode($jwt, $key, array('HS256'));


    }

    function authorizeAction ($token ){

    }
}