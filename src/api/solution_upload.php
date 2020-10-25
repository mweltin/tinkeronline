<?php

// composer auto loader
require 'header.php';

//grab http headers
$headers = apache_request_headers();
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



if( $has_permission->to('upload assets') ){
    try {
    
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
            
        $folderPath = "/home/tinkerblake/solution_uploads/";
       
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime-type extension
        $mime_type =finfo_file($finfo, $_FILES['file']['tmp_name']);
        finfo_close($finfo);

        // restrict solution upoad mime types
        $valid_file_mime_types = ['image/jpeg', 'image/png', 'image/gif' ];
        if( ! in_array($mime_type, $valid_file_mime_types)){
          throw new \Exception('Bad mine type for uploaded solution');
        }

        // restrict solution upload file size
        if ($_FILES['file']['size'] > 5000000) {
          throw new Exception('Exceeded file size limit of 5MB.');
        }

        $file_tmp = $_FILES['file']['tmp_name'];

        $file_ext = strtolower(end(explode('.',$_FILES['file']['name'])));
        $file = $folderPath . uniqid() . '.'.$file_ext;
        move_uploaded_file($file_tmp, $file);

        $chapterId = (int) $_POST['chapterId'];

        $challenge_id_query = <<<'SQL'
        SELECT challenge_id 
        FROM challenge
        WHERE chapter_id = ?
          AND account_id = ? 
SQL;
        $stmt = $pdo->prepare($challenge_id_query);
        $stmt->execute([ $chapterId, $tokenData['acct'] ]);
        $challenge_id = $stmt->fetchAll();

        $add_solution_query = <<<'SQL'
        INSERT INTO solution 
        ( challenge_id, 
        asset_path, 
        asset_type, 
        asset_name, 
        asset_temp_name, 
        approved)
        VALUES ( ?, ?, ?, ?, ?, ?)
SQL;
        $stmt = $pdo->prepare($add_solution_query);
        $stmt->execute(
          [ 
            $challenge_id[0]['challenge_id'], 
            $folderPath,
            $mime_type,
            $_FILES['file']['name'],
            $file,  
            0 ]
          );

        $response['message'] = $_FILES['file']['name']." uploaded";
    } catch (\Exception $e) {

        error_log( $e->getMessage() );
        $response['message'] = $e->getMessage();

    }
}

// issue new JWT
$jwt = $tm->issueTokenToUser($tokenData['acct']);
header('Authorzie: ' . $jwt);
header('Content-type: application/json');
print (  json_encode($response) );

exit();
?>