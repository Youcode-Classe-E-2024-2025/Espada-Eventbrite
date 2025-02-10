create DATABASE eventbrite;

CREATE TABLE role (
    id serial PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);


CREATE TABLE users (
    id serial  PRIMARY KEY,
    role_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL,
    avatar VARCHAR(255),
    banned TINYINT DEFAULT 0,
    archived TINYINT DEFAULT 0
);