<?php
// composer auto loader
require 'header.php';

//grab http headers
$headers = apache_request_headers();

// grab the user input from the registration.
$input = json_decode(file_get_contents('php://input'), true);
$token = $headers['Authorzie'];

// this action requires a valid token
$tm = new tokenManager($pdo, constant('JWT_KEY'));
try{
  $tm-> verifyToken( $token );
} catch( \Exception $e ) {
  error_log( $e->getMessage() );
  header( 'message: Token verification failure. Please login again.' );
  http_response_code (429);
  exit();
}

$tokenData = $tm->parseToken($token);

$has_permission = new hasPermission($pdo, $tokenData['acct']);


// check if they have edit settings permissions
$stmt = $pdo->prepare("SELECT * FROM account_action join action on action.action_id = account_action.action_id WHERE account_id=?");
$stmt->execute([ $tokenData['acct'] ]);
$result = $stmt->fetchAll();

if( $has_permission->to('update settings') ){
  switch($_SERVER["REQUEST_METHOD"]){
    case 'GET':
      $account_settings_query =<<<'SQL'
select username, email, billing_info 
from account 
join registrar on registrar.account_id = account.account_id
where account.account_id = ?;
SQL;

      $child_account_settings_query =<<<'SQL'
select email, username, account.account_id
from account 
join registrar on registrar.registrar_id = account.registrar_id
where account.account_id != ?
and registrar.account_id = ?;
SQL;

      $child_premission_settings_query =<<<'SQL'
select a.name,
    case 
       when aa.account_id is NULL then 'false' else 'true' 
    end as has_permission
from action a
left join account_action aa on aa.action_id  = a.action_id and aa.account_id = ?
where parent_only = 0
SQL;

      $assets_to_approve_query =<<<'SQL'
SELECT 
            solution.solution_id,
            solution.challenge_id,
            solution.asset_name,
            solution.asset_temp_name,
            solution.asset_type,
            solution.approved 
FROM solution
JOIN challenge ON solution.challenge_id = challenge.challenge_id
JOIN account child ON child.account_id = challenge.account_id
JOIN registrar ON registrar.registrar_id = child.registrar_id
JOIN account parent ON parent.account_id = registrar.account_id
WHERE 
  registrar.account_id = ?
AND 
  approved = 0
SQL;

      $stmt = $pdo->prepare( $account_settings_query  );
      $stmt->execute([ $tokenData['acct'] ]);
      $account_settings = $stmt->fetchAll();

      $stmt = $pdo->prepare( $child_account_settings_query );
      $stmt->execute([ $tokenData['acct'], $tokenData['acct']  ]);
      $child_account_settings = $stmt->fetchAll();

      foreach ($child_account_settings as $key => $val){
        $stmt = $pdo->prepare( $child_premission_settings_query );
        $stmt->execute([ $val['account_id'] ]);
        $child_premission_settings = $stmt->fetchAll();
        $child_account_settings[$key]['permissions'] = $child_premission_settings;
      } 

      $stmt = $pdo->prepare( $assets_to_approve_query );
      $stmt->execute([ $tokenData['acct'] ]);
      $assets_to_approve = $stmt->fetchAll();
      
      $response["user_settings"]["username"]= $account_settings[0]['username'];
      $response["user_settings"]["email"]= $account_settings[0]['email'];
      $response["user_settings"]["billing_info"]= $account_settings[0]['billing_info'];
      $response["child_settings"] = $child_account_settings;
      $response["assets_to_approve"] = $assets_to_approve;
    break;

    case 'POST':
      $input = json_decode(file_get_contents('php://input'), true);
      error_log(print_r($input, true));
      $update_account =<<<'SQL'
        update account 
          set username = ?,
              email = ?
        where account_id = ?
SQL;
      $update_registar_info =<<<'SQL'
        update registrar 
          set billing_info = ?
        where account_id = ?
SQL;
      $remove_permissions =<<<'SQL'
        delete from account_action where account_id = ? 
SQL;
      $add_permissions =<<<'SQL'
        insert into 
account_action (action_id, account_id) 
(select action_id, ? from action where name = ?) 
SQL;

      $stmt = $pdo->prepare( $update_account );
      $stmt->execute([ $input['username'], $input['email'], $tokenData['acct'] ]);

      $stmt = $pdo->prepare( $update_registar_info );
      $stmt->execute([ $input['billing_info'], $tokenData['acct'] ]);

      foreach( $input['children'] as $key => $val ){
        error_log("**".$key. print_r($val, true));
        $stmt = $pdo->prepare( $update_account );
        $stmt->execute([ $val['name'], $val['email'], $val['id'] ]);

        $stmt = $pdo->prepare( $remove_permissions );
        $stmt->execute([ $val['id'] ]);

        foreach( $val['perms'] as $name => $has_perm){
          if( $has_perm == 1){
            $stmt = $pdo->prepare( $add_permissions );
            $stmt->execute([ $val['id'], $name  ]);
          }
        }
      }
      $response = $input;
    break;
  }
} else {
  header( 'message: permissioned denied to update settings, please login.' );
  http_response_code (430);
  exit();
}

// issue new JWT
$jwt = $tm->issueTokenToUser($tokenData['acct']);
header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
