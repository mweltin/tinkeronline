DROP TABLE IF EXISTS account_action;

create table IF NOT EXISTS account_action (
    account_id integer auto_increment not null primary key,
    action_id integer
);
