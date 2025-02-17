CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    role_id INTEGER NOT NULL,
    email VARCHAR NOT NULL,
    password VARCHAR NOT NULL,
    username VARCHAR NOT NULL,
    banned INTEGER DEFAULT 0,
    archived INTEGER DEFAULT 0,
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    role_id INTEGER NOT NULL,
    email VARCHAR NOT NULL,
    password VARCHAR NOT NULL,
    username VARCHAR NOT NULL,
    banned INTEGER DEFAULT 0,
    archived INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    google_id VARCHAR,
    is_google BOOLEAN DEFAULT false,
    avatar TEXT DEFAULT 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNtzH5Fqm-sQfMI-caIpBxkv9aZpysLVHejQ&s'
);

CREATE TABLE role (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL
);
    google_id VARCHAR,
    is_google BOOLEAN DEFAULT false,
    avatar TEXT DEFAULT 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNtzH5Fqm-sQfMI-caIpBxkv9aZpysLVHejQ&s'
);

CREATE TABLE role (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL
);

CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    title VARCHAR NOT NULL
    id SERIAL PRIMARY KEY,
    title VARCHAR NOT NULL
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL,
    icon VARCHAR
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL,
    icon VARCHAR
);

CREATE TABLE evenments (
    id SERIAL PRIMARY KEY,
    title VARCHAR NOT NULL,
    description TEXT NOT NULL,
    visual_content VARCHAR,
    lieu VARCHAR NOT NULL,
    validation INTEGER DEFAULT 0,
    archived INTEGER DEFAULT 0,
    owner_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    date TIMESTAMP NOT NULL,
    type VARCHAR,
    video_path TEXT,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
CREATE TABLE evenments (
    id SERIAL PRIMARY KEY,
    title VARCHAR NOT NULL,
    description TEXT NOT NULL,
    visual_content VARCHAR,
    lieu VARCHAR NOT NULL,
    validation INTEGER DEFAULT 0,
    archived INTEGER DEFAULT 0,
    owner_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    date TIMESTAMP NOT NULL,
    type VARCHAR,
    video_path TEXT,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE capacity (
    id SERIAL PRIMARY KEY,
    evenment_id INTEGER NOT NULL,
    total_tickets INTEGER NOT NULL,
    vip_tickets_number INTEGER NOT NULL,
    vip_price DOUBLE PRECISION NOT NULL,
    standard_tickets_number INTEGER NOT NULL,
    standard_price DOUBLE PRECISION NOT NULL,
    gratuit_tickets_number INTEGER NOT NULL,
    early_bird_discount INTEGER,
    vip_tickets_sold INTEGER DEFAULT 0,
    standard_tickets_sold INTEGER DEFAULT 0,
    gratuit_tickets_sold INTEGER DEFAULT 0,
    FOREIGN KEY (evenment_id) REFERENCES evenments(id) ON DELETE CASCADE
    id SERIAL PRIMARY KEY,
    evenment_id INTEGER NOT NULL,
    total_tickets INTEGER NOT NULL,
    vip_tickets_number INTEGER NOT NULL,
    vip_price DOUBLE PRECISION NOT NULL,
    standard_tickets_number INTEGER NOT NULL,
    standard_price DOUBLE PRECISION NOT NULL,
    gratuit_tickets_number INTEGER NOT NULL,
    early_bird_discount INTEGER,
    vip_tickets_sold INTEGER DEFAULT 0,
    standard_tickets_sold INTEGER DEFAULT 0,
    gratuit_tickets_sold INTEGER DEFAULT 0,
    FOREIGN KEY (evenment_id) REFERENCES evenments(id) ON DELETE CASCADE
);

CREATE TABLE booking (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    evenment_id INTEGER NOT NULL,
    type VARCHAR NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    user_id INTEGER NOT NULL,
    evenment_id INTEGER NOT NULL,
    type VARCHAR NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    booking_date DATE NOT NULL,
    status TEXT DEFAULT '1',
    canceled INTEGER DEFAULT 0,
    qr_code_path TEXT,
    status TEXT DEFAULT '1',
    canceled INTEGER DEFAULT 0,
    qr_code_path TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evenment_id) REFERENCES evenments(id) ON DELETE CASCADE
);

CREATE TABLE envenment_tag (
    tag_id INTEGER NOT NULL,
    envenment_id INTEGER NOT NULL,
    PRIMARY KEY (tag_id, envenment_id),
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    FOREIGN KEY (envenment_id) REFERENCES evenments(id) ON DELETE CASCADE
);


INSERT INTO categories (name, icon) VALUES
('Concerts', 'fa-solid fa-music'),
('Conferences', 'fa-solid fa-chalkboard-teacher'),
('Workshops', 'fa-solid fa-tools'),
('Sports', 'fa-solid fa-football-ball'),
('Festivals', 'fa-solid fa-fire'),
('Networking', 'fa-solid fa-handshake'),
('Theater & Performing Arts', 'fa-solid fa-theater-masks'),
('Exhibitions', 'fa-solid fa-paint-brush'),
('Tech Events', 'fa-solid fa-laptop-code'),
('Charity & Fundraisers', 'fa-solid fa-hand-holding-heart');
