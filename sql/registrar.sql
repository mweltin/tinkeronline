DROP TABLE IF EXISTS registrar;

create TABLE IF NOT EXISTS registrar (
    id integer auto_increment not null primary key,
    login_id integer,
    billing_info text
);

