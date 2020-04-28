DROP TABLE IF EXISTS account;

create table IF NOT EXISTS account (
    account_id integer auto increment not null primary key, 
    username text,
    password text, 
    token text,
    registrar_id integer,
    email text,
);