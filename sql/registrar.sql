DROP TABLE IF EXISTS registrar;

create TABLE IF NOT EXISTS registrar (
    registrar_id integer auto_increment not null primary key,
    account_id integer,
    billing_info text
);

