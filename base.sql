-- ============================================================
--  Base SQLite - ProjetS4
-- ============================================================

PRAGMA foreign_keys = ON;

-- ------------------------------------------------------------
-- Table : operateur
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS operateur (
    id                   INTEGER PRIMARY KEY AUTOINCREMENT,
    nom                  TEXT    NOT NULL,
    est_notre_operateur  BOOLEAN NOT NULL DEFAULT 0,
    commission_exterieur REAL    NOT NULL DEFAULT 0.0,
    promotion REAL    NOT NULL DEFAULT 0.0
);

-- ------------------------------------------------------------
-- Table : prefixe_operateur
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS prefixe_operateur (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe      TEXT    NOT NULL UNIQUE,
    nom          TEXT    NOT NULL,
    id_operateur INTEGER,
    FOREIGN KEY (id_operateur) REFERENCES operateur(id)
);

-- ------------------------------------------------------------
-- Table : numero_telephone
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS numero_telephone (
    id           INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_prefixe   INTEGER  NOT NULL,
    numero       CHAR(10) NOT NULL UNIQUE,
    date_creation DATETIME NOT NULL DEFAULT (DATETIME('now')),
    FOREIGN KEY (id_prefixe) REFERENCES prefixe_operateur(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- Table : solde
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS solde (
    id          INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_numero_tel INTEGER NOT NULL,
    montant     REAL     NOT NULL DEFAULT 0.0,
    date        DATETIME NOT NULL DEFAULT (DATETIME('now')),
    FOREIGN KEY (id_numero_tel) REFERENCES numero_telephone(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- Table : type_operation
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS type_operation (
    id  INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT    NOT NULL UNIQUE
);

-- ------------------------------------------------------------
-- Table : tranches_frais
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tranches_frais (
    id                 INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_type_operation  INTEGER  NOT NULL,
    montant_min        REAL     NOT NULL,
    montant_max        REAL     NOT NULL,
    montant_frais      REAL     NOT NULL,
    date               DATETIME NOT NULL DEFAULT (DATETIME('now')),
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (montant_min >= 0),
    CHECK (montant_max > montant_min),
    CHECK (montant_frais >= 0)
);

-- ------------------------------------------------------------
-- Table : operation
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS operation (
    id                  INTEGER  PRIMARY KEY AUTOINCREMENT,
    id_type_operation   INTEGER  NOT NULL,
    id_numero_tel       INTEGER  NOT NULL,
    id_numero_tel_dest  INTEGER,
    montant             REAL     NOT NULL,
    frais               REAL     NOT NULL DEFAULT 0.0,
    commission          REAL     NOT NULL DEFAULT 0.0,
    date                DATETIME NOT NULL DEFAULT (DATETIME('now')),
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_numero_tel) REFERENCES numero_telephone(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_numero_tel_dest) REFERENCES numero_telephone(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (montant > 0),
    CHECK (frais >= 0),
    CHECK (id_numero_tel_dest IS NULL OR id_numero_tel_dest != id_numero_tel)
);

-- ============================================================
--  Données V2
-- ============================================================
INSERT INTO operateur (nom, est_notre_operateur, commission_exterieur) VALUES
('Telma', 1, 0.0),
('Airtel', 0, 0.02),
('Orange', 0, 0.02);

INSERT INTO prefixe_operateur (prefixe, nom, id_operateur) VALUES
('033', 'Airtel Money', 2),
('037', 'Orange Money', 3),
('034', 'Telma Mvola', 1);

INSERT INTO type_operation (nom) VALUES
('depot'),
('retrait'),
('transfert');

-- Barème retrait (id_type_operation = 2)
INSERT INTO tranches_frais (id_type_operation, montant_min, montant_max, montant_frais) VALUES
(2, 100,    1000,    50),
(2, 1001,   5000,    50),
(2, 5001,   10000,   100),
(2, 10001,  25000,   200),
(2, 25001,  50000,   400),
(2, 50001,  100000,  800),
(2, 100001, 250000,  1500),
(2, 250001, 500000,  1500),
(2, 500001, 1000000, 2500);

-- Barème transfert (id_type_operation = 3)
INSERT INTO tranches_frais (id_type_operation, montant_min, montant_max, montant_frais) VALUES
(3, 100,    1000,    100),
(3, 1001,   5000,    100),
(3, 5001,   10000,   200),
(3, 10001,  25000,   400),
(3, 25001,  50000,   800),
(3, 50001,  100000,  1500),
(3, 100001, 250000,  3000),
(3, 250001, 500000,  3000),
(3, 500001, 1000000, 5000);
