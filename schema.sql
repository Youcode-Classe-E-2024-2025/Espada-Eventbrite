-- Create database
CREATE DATABASE eventbrite2;

-- Connect to the new database (run manually in psql)
-- \c eventbrite2;

-- Table des utilisateurs
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des événements
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    location VARCHAR(255),
    capacity INTEGER,
    price DECIMAL(10, 2),
    category VARCHAR(50),
    organizer_id INTEGER,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_organizer FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des réservations (bookings)
CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    event_id INTEGER NOT NULL,
    type VARCHAR(50),
    price DECIMAL(10, 2),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'confirmed',
    CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_events_date ON events(start_date);
CREATE INDEX idx_booking_user ON bookings(user_id);
CREATE INDEX idx_booking_event ON bookings(event_id);

-- Données d'exemple
INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES 
('john_doe', 'john@example.com', '$2y$10$randomhashhere', 'John', 'Doe');

INSERT INTO events (title, description, start_date, end_date, location, capacity, price, category, organizer_id) VALUES 
('Conférence Tech 2025', 'Événement annuel sur les technologies émergentes', 
 '2025-06-15 09:00:00', '2025-06-17 18:00:00', 'Centre des Congrès', 500, 199.99, 'Technologie', 1);

INSERT INTO bookings (user_id, event_id, type, price) VALUES 
(1, 1, 'standard', 199.99);
