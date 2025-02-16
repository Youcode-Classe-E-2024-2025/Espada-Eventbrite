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

CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    title VARCHAR NOT NULL
);

CREATE TABLE categories (
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
);

CREATE TABLE booking (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    evenment_id INTEGER NOT NULL,
    type VARCHAR NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    booking_date DATE NOT NULL,
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
CREATE TABLE notification (
    id SERIAL PRIMARY KEY,
    from_id INTEGER NOT NULL,
    to_id INTEGER NOT NULL,
    action VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,checked int default 0 
    );
