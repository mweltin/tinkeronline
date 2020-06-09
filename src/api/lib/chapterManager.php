<?php

class chapterManager {

    private $account_id;
    private $pdo;

    function __construct($dbconn, $acct_id) {
        $this->pdo = $dbconn;
        $this->account_id = $acct_id;
    }

    function get_default_chapter(){
      $get_default_chapter_query = <<<'SQL'
      SELECT chapter, title, solved, challenge_id as accepted, chapter_id
      FROM chapter
      LEFT JOIN challenge on challenge.chapter_id = chapter.chapter_id
      WHERE (challenge.account_id = ? OR challenge.account_id IS NULL)
      order by chapter.date desc
      limit 1
SQL;
      $stmt = $this->pdo->prepare( $get_default_chapter_query );
      $stmt->execute([$this->account_id]);
      return $stmt->fetch();
    }

}
