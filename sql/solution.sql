DROP TABLE IF EXISTS solution;

create table IF NOT EXISTS solution (
    solution_id auto_increment not null primary key,
    challenge_id INTEGER,
    asset_path text ,
    assete_type text,
    approved boolean
);
