create DATABASE eventbrite;

CREATE TABLE role (  
    id serial PRIMARY KEY,  
    name VARCHAR(100) NOT NULL  
);  

CREATE TABLE users (  
    id serial PRIMARY KEY,  
    role_id INT NOT NULL,  
    email VARCHAR(255) NOT NULL,  
    password VARCHAR(255) NOT NULL,  
    username VARCHAR(100) NOT NULL,  
    avatar VARCHAR(255),  
    banned INT DEFAULT 0,  
    archived INT DEFAULT 0,  
    FOREIGN KEY (role_id) REFERENCES role(id)  
);






CREATE TABLE evenments (
    id serial PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    visual_content VARCHAR(255),
    lieu VARCHAR(255) NOT NULL,
    validation INT DEFAULT 0,
    archived INT DEFAULT 0,
    owner_id INT NOT NULL,
    category_id INT NOT NULL,
    date TIMESTAMP NOT NULL,
    type VARCHAR(255) CHECK (type IN ('public', 'private')),
    FOREIGN KEY (owner_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);


CREATE TABLE tags (
    id serial  PRIMARY KEY ,
    title VARCHAR(255) NOT NULL
);


CREATE TABLE categories (
    id serial PRIMARY KEY ,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(255)
);

CREATE TABLE envenment_tag (
    tag_id INT NOT NULL,
    envenment_id INT NOT NULL,
    PRIMARY KEY (tag_id, envenment_id),
    FOREIGN KEY (tag_id) REFERENCES tags(id),
    FOREIGN KEY (envenment_id) REFERENCES evenments(id)
);

CREATE TABLE capacity (
    id serial PRIMARY KEY ,
    evenment_id INT NOT NULL,
    total_tickets INT NOT NULL,
    vip_tickets_number INT NOT NULL,
    vip_price FLOAT NOT NULL,
    standard_tickets_number INT NOT NULL,
    standard_price FLOAT NOT NULL,
    gratuit_tickets_number INT NOT NULL,
    early_bird_discount INT,
    FOREIGN KEY (evenment_id) REFERENCES evenments(id)
);