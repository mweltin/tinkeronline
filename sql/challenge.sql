DROP TABLE IF EXISTS challenge;

create table IF NOT EXISTS challenge (
    challenge_id integer auto_increment not null primary key,
    account_id integer ,
    chapter_id integer,
    solved boolean
);
