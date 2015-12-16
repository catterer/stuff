create table if not exists users(_id integer primary key, name text);
create table if not exists messages(_id integer primary key, time datetime default current_timestamp, body text);

