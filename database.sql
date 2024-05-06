CREATE TABLE iF NOT exists ecritures (
    uuid VARCHAR(36) PRIMARY KEY,
    compte_uuid VARCHAR(36),
    label VARCHAR(255) NOT NULL DEFAULT '',
    date DATE NULL,
    type ENUM('C', 'D'),
    amount DOUBLE(14,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT current_timestamp(),
    updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    FOREIGN KEY (compte_uuid) REFERENCES comptes(uuid) ON UPDATE RESTRICT ON DELETE CASCADE
);

CREATE TABLE iF NOT exists comptes (
    uuid VARCHAR(36) PRIMARY KEY,
    login VARCHAR(255) NOT NULL DEFAULT '',
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT current_timestamp(),
    updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);  