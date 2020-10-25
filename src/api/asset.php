<?php
    require 'header.php';

    $asset_query =<<<'SQL'
    SELECT * 
    FROM solution 
    WHERE solution_id = ?
SQL;

    $stmt = $pdo->prepare( $asset_query );
    $stmt->execute([ (int)$_GET['id'] ]);
    $asset = $stmt->fetch();

    header('Content-Type: '. $asset['asset_type']);
    readfile( $asset['asset_temp_name'] );
?>