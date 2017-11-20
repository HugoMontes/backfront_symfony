CREATE DATABASE IF NOT EXISTS curso_backfront;
USE curso_backfront;

CREATE TABLE users(
	id 			int auto_increment not null,
	role		varchar(20),
	name		varchar(100),
	surname 	varchar(100),
	email		varchar(100),
	password 	varchar(255),
	created_at  datetime,
  updated_at  datetime,
	CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE tasks(
	id 			int auto_increment not null,
	user_id		int not null,
	title		varchar(255),
	description	text,
	status		varchar(100),
	created_at  datetime,
	updated_at  datetime,
	CONSTRAINT pk_tasks PRIMARY KEY(id),
	CONSTRAINT fk_tasks_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;
