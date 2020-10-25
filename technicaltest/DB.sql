CREATE DATABASE IF NOT EXISTS technicaltest;
USE technicaltest;

CREATE TABLE todo(
id              int(255) auto_increment not null,
title           varchar(255) not null,
description     text,
created_at      datetime DEFAULT CURRENT_TIMESTAMP,
done_at         datetime,
CONSTRAINT pk_todo PRIMARY KEY(id),
)ENGINE=InnoDb;


