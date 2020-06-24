    SELECT chapter, title, solved, challenge_id as accepted, chapter.chapter_id
      FROM chapter
      LEFT JOIN challenge on challenge.chapter_id = chapter.chapter_id
      WHERE (challenge.account_id = 90 OR challenge.account_id IS NULL)
      order by chapter.date desc
      limit 1


select * from account;
