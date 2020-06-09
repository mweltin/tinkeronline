<?php

class hasPermission {

    private $account_id;
    private $pdo;
    private $permissions;

    function __construct($dbconn, $acct_id) {
        $this->pdo = $dbconn;
        $this->account_id = $acct_id;

        // check for permissions to read chapters
        $permissions_query = <<<'SQL'
        SELECT *
        FROM account_action
        JOIN action ON action.action_id = account_action.action_id
        WHERE account_id=?
SQL;
        $stmt = $this->pdo->prepare($permissions_query);
        $stmt->execute([ $this->account_id ]);
        $this->permissions = $stmt->fetchAll();
    }

    function to($action){
        $return_value = FALSE;

        if( array_search($action, array_column($this->permissions, 'name')) !== FALSE ){
          $return_value = TRUE;
        }

        return $return_value;
    }

}
