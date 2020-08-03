DROP TABLE IF EXISTS action;

create table IF NOT EXISTS action (
    action_id integer auto_increment not null primary key,
    name varchar(20) not null unique,
    description text,
    parent_only BOOLEAN default 0
);

INSERT INTO action (name,description, parent_only)
VALUES
('view content', 'user is allowed to view content', 0),
('accept challenges', 'user is allowed to enter into a challenge', 0),
('upload assets', 'User is allowed to upload solutions', 0),
('approve assets', 'User is allowed to approve uploaded solutions', 1),
('update settings', 'Parent account setting that allows them to change settings for learners', 1);
