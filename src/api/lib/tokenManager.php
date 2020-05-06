<?php
// library to create JW tokens to be issued after a successful registration
use \Firebase\JWT\JWT;

namespace tokenManager;

require('../header.php');

class tokenManager {

    function issueToken($account_id){
        // create JWT token 
        $payload = array(
            "iss" => "https://tinkercamp.org",
            "iat" => time(),
            "exp" => time() + (2 * 60 * 60),
            "acct" => $account_id
        );

        return $jwt = JWT::encode($payload, $_JWT_KEY);
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