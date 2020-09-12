DROP TABLE IF EXISTS solution;

create table IF NOT EXISTS solution (
    solution_id INTEGER auto_increment not null primary key,
    challenge_id INTEGER,
    asset_path text,
    asset_name text,
    asset_temp_name text,
    asset_type text,
    approved boolean
);
