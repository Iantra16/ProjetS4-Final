-- ============================================================
--  Base SQLite - ProjetS4
--  Créé le : 2026-07-20
-- ============================================================

PRAGMA foreign_keys = ON;

-- ------------------------------------------------------------
-- Table : prefixe_operateur
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS prefixe_operateur (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT    NOT NULL UNIQUE,
    nom     TEXT    NOT NULL
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
    id_numero_tel       INTEGER  NOT NULL,  -- compte "source" (celui qui initie)
    id_numero_tel_dest  INTEGER,            -- NULL sauf pour un transfert
    montant             REAL     NOT NULL,
    frais               REAL     NOT NULL DEFAULT 0.0,
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
--  Données de test - ProjetS4 Mobile Money
-- ============================================================

INSERT INTO prefixe_operateur (prefixe, nom) VALUES
('033', 'Airtel Money'),
('037', 'Orange Money'),
('034', 'Telma Mvola');

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

INSERT INTO numero_telephone (id_prefixe, numero, date_creation) VALUES
(1, '0331234567', '2026-07-01 08:00:00'),  -- Airtel
(1, '0339876543', '2026-07-02 09:15:00'),  -- Airtel
(2, '0371112233', '2026-07-01 10:00:00'),  -- Orange
(2, '0374445566', '2026-07-03 14:30:00'),  -- Orange
(3, '0347778899', '2026-07-04 16:00:00');  -- Telma

INSERT INTO solde (id_numero_tel, montant, date) VALUES
(1, 0,       '2026-07-01 08:00:00'),   -- création compte 1
(1, 50000,   '2026-07-05 10:00:00'),   -- après dépôt
(2, 0,       '2026-07-02 09:15:00'),
(2, 120000,  '2026-07-06 11:00:00'),
(3, 0,       '2026-07-01 10:00:00'),
(3, 30000,   '2026-07-04 15:00:00'),
(4, 0,       '2026-07-03 14:30:00'),
(4, 15000,   '2026-07-07 09:00:00'),
(5, 0,       '2026-07-04 16:00:00'),
(5, 200000,  '2026-07-08 12:00:00');

-- Dépôts (frais = 0 selon ton exemple)
INSERT INTO operation (id_type_operation, id_numero_tel, montant, frais, date) VALUES
(1, 1, 50000, 0, '2026-07-05 10:00:00'),   -- dépôt compte 1
(1, 2, 120000, 0, '2026-07-06 11:00:00'),  -- dépôt compte 2
(1, 3, 30000, 0, '2026-07-04 15:00:00'),   -- dépôt compte 3
(1, 4, 15000, 0, '2026-07-07 09:00:00'),   -- dépôt compte 4
(1, 5, 200000, 0, '2026-07-08 12:00:00');  -- dépôt compte 5

-- Retrait : compte 1 retire 5000 → tranche retrait [1001-5000] = 50 Ar de frais
INSERT INTO operation (id_type_operation, id_numero_tel, montant, frais, date) VALUES
(2, 1, 5000, 50, '2026-07-09 08:30:00');

-- Transfert : compte 2 envoie 10000 vers compte 4 → tranche transfert [5001-10000] = 200 Ar de frais
INSERT INTO operation (id_type_operation, id_numero_tel, id_numero_tel_dest, montant, frais, date) VALUES
(3, 2, 4, 10000, 200, '2026-07-09 09:00:00');

-- Transfert : compte 5 envoie 25000 vers compte 3 → tranche transfert [10001-25000] = 400 Ar de frais
INSERT INTO operation (id_type_operation, id_numero_tel, id_numero_tel_dest, montant, frais, date) VALUES
(3, 5, 3, 25000, 400, '2026-07-09 09:30:00');