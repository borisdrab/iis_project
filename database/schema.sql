DROP TABLE IF EXISTS Komentar;
DROP TABLE IF EXISTS Zaplatil;
DROP TABLE IF EXISTS Registracia;
DROP TABLE IF EXISTS Vstupne;
DROP TABLE IF EXISTS UdalostFoto;
DROP TABLE IF EXISTS MiestoFoto;
DROP TABLE IF EXISTS Udalost;
DROP TABLE IF EXISTS Kategorie;
DROP TABLE IF EXISTS Miesto;
DROP TABLE IF EXISTS Administrator;
DROP TABLE IF EXISTS Moderator;
DROP TABLE IF EXISTS Uzivatel;
DROP TABLE IF EXISTS Osoba;

CREATE TABLE Osoba (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meno VARCHAR(100) NOT NULL,
    kontakt VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    heslo_hash VARCHAR(255) NOT NULL,
    aktivny BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE Uzivatel (
    id INT PRIMARY KEY,
    vek INT,
    pohlavie VARCHAR(20),

    CONSTRAINT fk_uzivatel_osoba FOREIGN KEY (id) REFERENCES Osoba(id) ON DELETE CASCADE,
    CONSTRAINT chk_uzivatel_vek CHECK (vek IS NULL OR (vek >= 0 AND vek <= 110))
);

CREATE TABLE Moderator (
    id INT PRIMARY KEY,

    CONSTRAINT fk_moderator_osoba FOREIGN KEY (id) REFERENCES Osoba(id) ON DELETE CASCADE
);

CREATE TABLE Administrator (
    id INT PRIMARY KEY,

    CONSTRAINT fk_administrator_moderator FOREIGN KEY (id) REFERENCES Moderator(id) ON DELETE CASCADE
);

CREATE TABLE Miesto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adresa VARCHAR(255) NOT NULL,
    typ VARCHAR(100),
    popis TEXT,
    navrhol_id INT,
    stav VARCHAR(20) NOT NULL,
    schvalil_id INT,

    CONSTRAINT fk_miesto_navrhol FOREIGN KEY (navrhol_id) REFERENCES Uzivatel(id),
    CONSTRAINT fk_miesto_schvalil FOREIGN KEY (schvalil_id) REFERENCES Moderator(id)
);

CREATE TABLE Kategorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    typ VARCHAR(100) NOT NULL,
    rodic_id INT,
    navrhol_id INT,
    stav VARCHAR(20) NOT NULL,
    schvalil_id INT,

    CONSTRAINT fk_kategorie_rodic FOREIGN KEY (rodic_id) REFERENCES Kategorie(id),
    CONSTRAINT fk_kategorie_navrhol FOREIGN KEY (navrhol_id) REFERENCES Uzivatel(id),
    CONSTRAINT fk_kategorie_schvalil FOREIGN KEY (schvalil_id) REFERENCES Moderator(id),
)

CREATE TABLE Udalost (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oznacenie VARCHAR(150) NOT NULL UNIQUE,
    datum_od DATE NOT NULL,
    cas_od TIME NOT NULL,
    datum_do DATE NOT NULL,
    cas_do TIME NOT NULL,
    vedenie_sedenia VARCHAR(255),
    -- kapacita = NULL - neobmedzena kapacita
    kapacita INT,
    popis TEXT,
    ikona VARCHAR(255),
    miesto_id INT NOT NULL,
    kategoria_id INT NOT NULL,
    zakladatel_id INT NOT NULL,
    stav VARCHAR(20) NOT NULL,
    schvalil_id INT,

    CONSTRAINT fk_udalost_miesto FOREIGN KEY (miesto_id) REFERENCES Miesto(id),
    CONSTRAINT fk_udalost_kategoria FOREIGN KEY (kategoria_id) REFERENCES Kategorie(id),
    CONSTRAINT fk_udalost_zakladatel FOREIGN KEY (zakladatel_id) REFERENCES Uzivatel(id),
    CONSTRAINT fk_udalost_schvalil FOREIGN KEY (schvalil_id) REFERENCES Moderator(id),
    CONSTRAINT chk_udalost_kapacita CHECK (kapacita IS NULL OR kapacita > 0)
);

CREATE TABLE UdalostFoto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udalost_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,

    CONSTRAINT fk_udalostfoto_udalost FOREIGN KEY (udalost_id) REFERENCES Udalost(id) ON DELETE CASCADE
);

CREATE TABLE MiestoFoto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    miesto_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,

    CONSTRAINT fk_miestofoto_miesto FOREIGN KEY (miesto_id) REFERENCES Miesto(id) ON DELETE CASCADE
);

CREATE TABLE Vstupne (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cennik DECIMAL(10,2) NOT NULL,
    typ VARCHAR(100) NOT NULL,
    udalost_id INT NOT NULL,

    CONSTRAINT fk_vstupne_udalost FOREIGN KEY (udalost_id) REFERENCES Udalost(id) ON DELETE CASCADE,
    CONSTRAINT chk_vstupne_cennik CHECK (cennik >= 0)
);

CREATE TABLE Registracia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uzivatel_id INT NOT NULL,
    udalost_id INT NOT NULL,
    vstupne_id INT,

    CONSTRAINT fk_registracia_uzivatel FOREIGN KEY (uzivatel_id) REFERENCES Uzivatel(id),
    CONSTRAINT fk_registracia_udalost FOREIGN KEY (udalost_id) REFERENCES Udalost(id),
    CONSTRAINT fk_registracia_vstupne FOREIGN KEY (vstupne_id) REFERENCES Vstupne(id),
    -- jeden uzivatel sa na konkretnu udalost moze zaregistrovat iba raz
    CONSTRAINT uq_registracia_uzivatel_udalost UNIQUE (uzivatel_id, udalost_id)
);

CREATE TABLE Zaplatil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- jedna registracia max jeden zaznam o zaplateni
    registracia_id INT NOT NULL UNIQUE,
    zaplatene BOOLEAN NOT NULL DEFAULT FALSE,

    CONSTRAINT fk_zaplatil_registracia FOREIGN KEY (registracia_id) REFERENCES Registracia(id) ON DELETE CASCADE
);

CREATE TABLE Komentar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udalost_id INT NOT NULL,
    uzivatel_id INT NOT NULL,
    text TEXT,
    hodnotenie INT,
    moderoval_id INT,

    CONSTRAINT fk_komentar_udalost FOREIGN KEY (udalost_id) REFERENCES Udalost(id) ON DELETE CASCADE,
    CONSTRAINT fk_komentar_uzivatel FOREIGN KEY (uzivatel_id) REFERENCES Uzivatel(id),
    CONSTRAINT fk_komentar_moderoval FOREIGN KEY (moderoval_id) REFERENCES Moderator(id),
    CONSTRAINT chk_komentar_hodnotenie CHECK (hodnotenie IS NULL OR hodnotenie BETWEEN 1 AND 5)
);