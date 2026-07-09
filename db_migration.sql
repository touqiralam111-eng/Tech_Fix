-- db_migration.sql - PostgreSQL version for Render

-- Create tables
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    user_type VARCHAR(20) DEFAULT 'user',
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS service_requests (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    service_type VARCHAR(100),
    description TEXT,
    urgency VARCHAR(20) DEFAULT 'medium',
    status VARCHAR(20) DEFAULT 'pending',
    assigned_admin INTEGER REFERENCES users(id),
    completion_notes TEXT,
    preferred_date DATE,
    budget DECIMAL(10,2),
    attachment VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contacts (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    attachment VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS site_visits (
    id SERIAL PRIMARY KEY,
    ip_address VARCHAR(45),
    user_agent TEXT,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    page_visited VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS user_sessions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    session_id VARCHAR(255),
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    ip_address VARCHAR(45)
);

-- Insert default admin user
INSERT INTO users (username, email, password, user_type) 
SELECT 'admin', 'admin@techfix.com', '$2y$10$YourHashedPasswordHere', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@techfix.com');

-- Insert default user
INSERT INTO users (username, email, password, user_type) 
SELECT 'user1', 'user@techfix.com', '$2y$10$YourHashedPasswordHere', 'user'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'user@techfix.com');
