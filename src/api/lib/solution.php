<?php

require('../constanats');

class solution {

    private $getSolutionByNameQuery =<<<'SQL'
        SELECT * 
        FROM solution
        WHERE asset_name = ? 
SQL;

    private $approveSolutionQuery =<<<'SQL'
        UPDATE solution
        SET approved = 1
        WHERE solution_id = ? 
SQL;

    function __construct($dbconn)
    {
        $this->pdo = $dbconn;
        $this->soultionDetail = null;
        $this->sourceDir = constant("UPLOAD_ASSETS_DIR");
        $this->destinationDir = constant("APPROVED_ASSET_DIR");
    }

    private function getSolutionByName($name) {
        $stmt = $this->pdo->prepare($this->getSolutionByNameQuery);
        $stmt->execute([ $name ]);
        $this->soultionDetail = $stmt->fetch();
    }

    private function moveSolutionUnderDocRoot() {
        
        rename($this->soultionDetail['asset_temp_name'] , $this->destinationDir."/".)
    }

    public function approveSolutionByName($name = false) {
        if( ! $name ) return false;

        $this->getSolutionByName($name);
        $stmt = $this->pdo->prepare($this->approveSolutionQuery);
        error_log("got here to approve: ". $name. "  ".$this->soultionDetail['solution_id']);

        $stmt->execute([$this->soultionDetail['solution_id']]);

    }
}