-- Add google_id column to users table
ALTER TABLE users
ADD COLUMN google_id VARCHAR(255) DEFAULT NULL;

-- Add unique constraint to prevent duplicate Google accounts
ALTER TABLE users
ADD CONSTRAINT unique_google_id UNIQUE (google_id);
