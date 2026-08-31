-- BudgetKit schema for Supabase (PostgreSQL)
-- Run once in Supabase → SQL Editor → New query → Run

CREATE TABLE IF NOT EXISTS migrations (
    id SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

-- 0001_01_01_000000_create_users_table
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id);
CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity);

-- 0001_01_01_000001_create_cache_table
CREATE TABLE IF NOT EXISTS cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- 0001_01_01_000002_create_jobs_table
CREATE TABLE IF NOT EXISTS jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS jobs_queue_index ON jobs (queue);

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 2026_02_14_000001_create_categories_table
CREATE TABLE IF NOT EXISTS categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    emoji VARCHAR(10) NOT NULL DEFAULT '',
    color VARCHAR(7) NOT NULL DEFAULT '#6B7280',
    budget_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    is_goal BOOLEAN NOT NULL DEFAULT FALSE,
    target_amount DECIMAL(10, 2) NULL,
    target_date DATE NULL,
    sort_order SMALLINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

INSERT INTO categories (name, emoji, color, sort_order, is_goal, created_at, updated_at)
SELECT * FROM (VALUES
    ('Affitto', '🏠', '#EF4444', 1::smallint, FALSE, NOW(), NOW()),
    ('Spesa', '🛒', '#F97316', 2::smallint, FALSE, NOW(), NOW()),
    ('Ristoranti', '🍕', '#EAB308', 3::smallint, FALSE, NOW(), NOW()),
    ('Trasporti', '🚗', '#3B82F6', 4::smallint, FALSE, NOW(), NOW()),
    ('Bollette', '💡', '#A855F7', 5::smallint, FALSE, NOW(), NOW()),
    ('Divertimento', '🎉', '#EC4899', 6::smallint, FALSE, NOW(), NOW()),
    ('Shopping', '👕', '#14B8A6', 7::smallint, FALSE, NOW(), NOW()),
    ('Risparmi', '💰', '#22C55E', 8::smallint, FALSE, NOW(), NOW()),
    ('Salute', '🏥', '#06B6D4', 9::smallint, FALSE, NOW(), NOW()),
    ('Altro', '❓', '#6B7280', 10::smallint, FALSE, NOW(), NOW())
) AS v(name, emoji, color, sort_order, is_goal, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM categories LIMIT 1);

-- 2026_02_14_000002_create_transactions_table
CREATE TABLE IF NOT EXISTS transactions (
    id BIGSERIAL PRIMARY KEY,
    type VARCHAR(255) NOT NULL CHECK (type IN ('income', 'expense')),
    amount DECIMAL(10, 2) NOT NULL,
    category_id BIGINT NULL REFERENCES categories(id) ON DELETE SET NULL,
    date DATE NOT NULL,
    note VARCHAR(255) NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

-- 2026_02_14_000003_create_budgets_table
CREATE TABLE IF NOT EXISTS budgets (
    id BIGSERIAL PRIMARY KEY,
    category_id BIGINT NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    year SMALLINT NOT NULL,
    month SMALLINT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    UNIQUE (category_id, year, month)
);

-- 2026_02_14_000005_create_settings_table
CREATE TABLE IF NOT EXISTS settings (
    key VARCHAR(255) PRIMARY KEY,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

INSERT INTO settings (key, value, created_at, updated_at) VALUES
    ('locale', 'en', NOW(), NOW()),
    ('currency', 'EUR', NOW(), NOW())
ON CONFLICT (key) DO NOTHING;

-- 2026_03_03_000001_add_translation_key_to_categories_table
ALTER TABLE categories ADD COLUMN IF NOT EXISTS translation_key VARCHAR(255) NULL;

UPDATE categories SET translation_key = 'rent'           WHERE name = 'Affitto'      AND translation_key IS NULL;
UPDATE categories SET translation_key = 'groceries'      WHERE name = 'Spesa'        AND translation_key IS NULL;
UPDATE categories SET translation_key = 'restaurants'    WHERE name = 'Ristoranti'   AND translation_key IS NULL;
UPDATE categories SET translation_key = 'transport'      WHERE name = 'Trasporti'    AND translation_key IS NULL;
UPDATE categories SET translation_key = 'bills'          WHERE name = 'Bollette'     AND translation_key IS NULL;
UPDATE categories SET translation_key = 'entertainment'    WHERE name = 'Divertimento' AND translation_key IS NULL;
UPDATE categories SET translation_key = 'shopping'       WHERE name = 'Shopping'     AND translation_key IS NULL;
UPDATE categories SET translation_key = 'savings'        WHERE name = 'Risparmi'     AND translation_key IS NULL;
UPDATE categories SET translation_key = 'health'         WHERE name = 'Salute'       AND translation_key IS NULL;
UPDATE categories SET translation_key = 'other'          WHERE name = 'Altro'        AND translation_key IS NULL;

-- 2026_04_05_140321_add_receipt_path_to_transactions_table
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS receipt_path VARCHAR(255) NULL;

-- Mark migrations as run (so artisan migrate won't try again later)
INSERT INTO migrations (migration, batch)
SELECT migration, batch FROM (VALUES
    ('0001_01_01_000000_create_users_table', 1),
    ('0001_01_01_000001_create_cache_table', 1),
    ('0001_01_01_000002_create_jobs_table', 1),
    ('2026_02_14_000001_create_categories_table', 1),
    ('2026_02_14_000002_create_transactions_table', 1),
    ('2026_02_14_000003_create_budgets_table', 1),
    ('2026_02_14_000005_create_settings_table', 1),
    ('2026_03_03_000001_add_translation_key_to_categories_table', 1),
    ('2026_04_05_140321_add_receipt_path_to_transactions_table', 1)
) AS m(migration, batch)
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = m.migration);
