DROP TABLE IF EXISTS chapter;

create table IF NOT EXISTS chapter (
    chapter_id integer auto_increment not null primary key,
    chapter text,
    title text,
    date timestamp
);
