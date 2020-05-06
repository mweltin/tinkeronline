<?php
// library to create JW tokens to be issued after a successful registration
use \Firebase\JWT\JWT;

class tokenManager {

    private $jwt_key;
    private $pdo; 

    function __construct($dbconn, $key) {
        $this->pdo = $dbconn;
        $this->jtw_key = $key;
    }
    
    function issueTokenToUser($account_id){
        // create JWT token 
        $payload = array(
            "iss" => "https://tinkercamp.org",
            "iat" => time(),
            "exp" => time() + (2 * 60 * 60),
            "acct" => $account_id
        );

        $jwt = JWT::encode($payload, $this->jtw_key);

        $add_token = <<<'SQL'
            INSERT INTO token (token)
            VALUES (?)
SQL;
        $stmt = $this->pdo->prepare( $add_token );
        $stmt->execute([ $jwt ]); 
        $token_id = $this->pdo->lastInsertId();

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
        
        $decoded = JWT::decode($jwt, $this->jtw_key, array('HS256'));
    }

    function authorizeAction ($token ){

    }
}