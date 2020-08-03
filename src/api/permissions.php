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
      
      $response["user_settings"]["username"]= $account_settings[0]['username'];
      $response["user_settings"]["email"]= $account_settings[0]['email'];
      $response["user_settings"]["billing_info"]= $account_settings[0]['billing_info'];
      $response["child_settings"] = $child_account_settings;
    break;

    case 'POST':
      $input = json_decode($HTTP_RAW_POST_DATA, true);
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
