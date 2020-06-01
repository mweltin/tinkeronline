DROP TABLE IF EXISTS account;

create table IF NOT EXISTS account (
    account_id integer auto_increment not null primary key,
    username varchar(20) not null unique,
    passwd text,
    token_id integer,
    registrar_id integer,
    email text
);
