-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR NOT NULL,
    email VARCHAR UNIQUE NOT NULL,
    email_verified_at DATETIME NULL,
    password VARCHAR NOT NULL,
    phone VARCHAR NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    status VARCHAR DEFAULT 'active',
    remember_token VARCHAR NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

-- Create investments table
CREATE TABLE IF NOT EXISTS investments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title VARCHAR NOT NULL,
    description TEXT NULL,
    amount DECIMAL(15,2) NOT NULL,
    expected_return DECIMAL(15,2) NOT NULL,
    actual_return DECIMAL(15,2) DEFAULT 0.00,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    status VARCHAR DEFAULT 'active',
    type VARCHAR DEFAULT 'other',
    metadata TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

-- Create transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    investment_id INTEGER NULL,
    amount DECIMAL(15,2) NOT NULL,
    type VARCHAR NOT NULL,
    description TEXT NULL,
    status VARCHAR DEFAULT 'pending',
    metadata TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

-- Create indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_investments_user_id ON investments(user_id);
CREATE INDEX idx_transactions_user_id ON transactions(user_id);
CREATE INDEX idx_transactions_investment_id ON transactions(investment_id);
