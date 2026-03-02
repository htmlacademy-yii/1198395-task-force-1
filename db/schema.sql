CREATE DATABASE IF NOT EXISTS taskforce
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE taskforce;

CREATE TABLE IF NOT EXISTS categories
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    name        VARCHAR(128) NOT NULL,
    icon        VARCHAR(128) NOT NULL
    );

CREATE TABLE IF NOT EXISTS cities
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    name        VARCHAR(256) NOT NULL,
    lat         DECIMAL NOT NULL,
    long        DECIMAL NOT NULL
    );

CREATE TABLE IF NOT EXISTS users
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    email       VARCHAR(256) NOT NULL UNIQUE,
    name        VARCHAR(256) NOT NULL,
    city_id     INT UNSIGNED NOT NULL,
    password    VARCHAR(128) NOT NULL,
    is_executor BOOLEAN      NOT NULL,
    profile_img VARCHAR(256),
    birthday    DATE,
    phone       CHAR(11),
    telegram    CHAR(64),
    about       TEXT
    );

CREATE TABLE IF NOT EXISTS tasks
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    author_id   INT UNSIGNED NOT NULL,
    executor_id INT UNSIGNED,
    name        VARCHAR(256) NOT NULL,
    description TEXT         NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    location    VARCHAR(256) NOT NULL,
    lat         DECIMAL,
    long        DECIMAL,
    city_id     INT UNSIGNED,
    budget      INT UNSIGNED,
    expire_date DATE,
    status      ENUM('status_new', 'status_canceled', 'status_active', 'status_finished', 'status_failed'),
    FOREIGN KEY (author_id) REFERENCES users (id),
    FOREIGN KEY (executor_id) REFERENCES users (id),
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FOREIGN KEY (city_id) REFERENCES cities (id)
    );

CREATE TABLE IF NOT EXISTS task_files
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    task_id     INT UNSIGNED NOT NULL,
    url         VARCHAR(256) NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks (id)
    );

CREATE TABLE IF NOT EXISTS user_categories
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id     INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
    );

CREATE TABLE IF NOT EXISTS reviews
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    author_id   INT UNSIGNED NOT NULL,
    executor_id INT UNSIGNED NOT NULL,
    task_id     INT UNSIGNED NOT NULL,
    comment     TEXT NOT NULL,
    rating      INT UNSIGNED NOT NULL,
    CONSTRAINT  chk_rating CHECK (rating BETWEEN 1 AND 5),
    FOREIGN KEY (task_id) REFERENCES tasks (id),
    FOREIGN KEY (author_id) REFERENCES users (id),
    FOREIGN KEY (executor_id) REFERENCES users (id)
    );

CREATE TABLE IF NOT EXISTS responds
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    task_id     INT UNSIGNED NOT NULL,
    executor_id INT UNSIGNED NOT NULL,
    comment     TEXT,
    price       INT UNSIGNED,
    rejected    BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (task_id) REFERENCES tasks (id),
    FOREIGN KEY (executor_id) REFERENCES users (id)
    );

CREATE INDEX u_date ON users (created_at);
CREATE INDEX u_email ON users (email);
CREATE INDEX t_date ON tasks (created_at);
