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
        $exp_time = time() + (2 * 60 * 60);
        $payload = array(
            "iss" => "https://tinkercamp.org",
            "iat" => time(),
            "exp" => $exp_time,
            "acct" => $account_id
        );

        $jwt = JWT::encode($payload, $this->jtw_key);

        $add_token = <<<'SQL'
            INSERT INTO token (token, expires)
            VALUES (?, ?)
SQL;
        $stmt = $this->pdo->prepare( $add_token );
        $stmt->execute([ $jwt, $exp_time ]);
        $token_id = $this->pdo->lastInsertId();

        $assign_token_to_user = <<< 'SQL'
            UPDATE account
                SET token_id = ?
            WHERE
                account_id = ?
SQL;
        $stmt = $this->pdo->prepare( $assign_token_to_user );
        $stmt->execute([$token_id, $account_id]);

        return $jwt;
    }

    /**
     * Verify the token is valid
     * Check the database for the last token issued to the user and
     * ensure the payloads match and the token hasn't expired.
     */
    function verifyToken( $token ){

        $decoded = $this->parseToken($token);

        $get_token = <<<'SQL'
        SELECT token.* FROM account
        JOIN token ON token.token_id = account.token_id
        WHERE account_id = (?)
SQL;
        $stmt = $this->pdo->prepare( $get_token );
        $stmt->execute([ $decoded['acct'] ]);
        $stored_token = $stmt->fetch();

        if( $stored_token['token'] != $token){
            throw new \Exception('Token mismatch.');
        }

        if(time() > $decoded['exp']){
            throw new \Exception('Token expired.');
        }

        // if the return token matches what is stored in the DB
        // hasn't expired its a valid token
        return true;
    }


    function parseToken($token){
      $decoded = JWT::decode($token, $this->jtw_key, array('HS256'));
      return (array)$decoded;
    }


    function authorizeAction ( $token, $action ){
        try{
            $this->verifyToken($token);
        } catch(\Exception $e) {
            return false;
        }

        $decoded = JWT::decode($token, $this->jtw_key, array('HS256'));

        $getPerm = <<<'SQL'
        SELECT  count(1) AS has_permission FROM account
        JOIN account_action ON account.account_id = account_action.account_id
        JOIN action ON action.action_id = account_action.action_id
        WHERE  action = (?)
SQL;
        $stmt = $this->pdo->prepare( $getPerm );
        $stmt->execute([ $action ]);

        if($stmt->rowCount() > 0 ){
            return true;
        }

        return false;
    }
}
