DROP TABLE IF EXISTS action;

create table IF NOT EXISTS action (
    action_id integer auto_increment not null primary key, 
    name varchar(20) not null unique
);

INSERT INTO action (name)
VALUES
('view content'),
('upload assets'),
('approve assets');