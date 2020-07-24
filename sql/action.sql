DROP TABLE IF EXISTS action;

create table IF NOT EXISTS action (
    action_id integer auto_increment not null primary key,
    name varchar(20) not null unique,
    description text
);

INSERT INTO action (name,description)
VALUES
('view content', 'user is allowed to view content'),
('accept challenges', 'user is allowed to enter into a challenge'),
('upload assets', 'User is allowed to upload solutions'),
('approve assets', 'User is allowed to approve uploaded solutions'),
('update settings', 'Parent account setting that allows them to change settings for learners');
