DROP TABLE IF EXISTS token;

create table IF NOT EXISTS token (
    token_id integer auto_increment not null primary key, 
    token text not null,
    issueed timestamp default NOW()
);