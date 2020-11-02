<?php

require 'constants.php';

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

    private $markChallengeSolvedQuery =<<<'SQL'
        UPDATE challenge
        SET solved = 1
        WHERE challenge_id = ? 
SQL;

    function __construct($dbconn)
    {
        $this->pdo = $dbconn;
        $this->soultionDetail = null;
        $this->sourceDir = constant("UPLOAD_ASSET_DIR");
        $this->destinationDir = constant("APPROVED_ASSET_DIR");
    }

    private function getSolutionByName($name) {
        $stmt = $this->pdo->prepare($this->getSolutionByNameQuery);
        $stmt->execute([ $name ]);
        $this->soultionDetail = $stmt->fetch();
    }

    private function moveSolutionUnderDocRoot() {
        rename($this->sourceDir . $this->soultionDetail['asset_temp_name'] , $this->destinationDir . $this->soultionDetail['asset_temp_name']);
    }

    private function approveSolution() {
        $stmt = $this->pdo->prepare($this->approveSolutionQuery);
        $stmt->execute([ $this->soultionDetail['solution_id'] ]);
        $this->soultionDetail['approved'] = true;
    }

    private function markChallengeSolved() {
        $stmt = $this->pdo->prepare($this->markChallengeSolvedQuery);
        $stmt->execute([ $this->soultionDetail['challenge_id'] ]);
        $this->soultionDetail['solved'] = true;
    }

    public function approveSolutionByName($name = false) {
        if( ! $name ) return false;

        $this->getSolutionByName($name);
        $stmt = $this->pdo->prepare($this->approveSolutionQuery);
        $stmt->execute([$this->soultionDetail['solution_id']]);
        $this->moveSolutionUnderDocRoot();
        $this->approveSolution();
        $this->markChallengeSolved();

    }
}