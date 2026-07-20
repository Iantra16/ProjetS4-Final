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
    id            INTEGER  PRIMARY KEY AUTOINCREMENT,
    montant_min   REAL     NOT NULL,
    montant_max   REAL     NOT NULL,
    montant_frais REAL     NOT NULL,
    date          DATETIME NOT NULL DEFAULT (DATETIME('now')),
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