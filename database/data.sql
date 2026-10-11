INSERT INTO Osoba (id, meno, kontakt, email, heslo_hash, aktivny) VALUES
(1, 'Ján Novák', '+421900111111', 'jan.novak@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
(2, 'Petra Horváthová', '+421900222222', 'petra.horvathova@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
(3, 'Martin Moderátor', '+421900333333', 'moderator@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
(4, 'Adam Administrátor', '+421900444444', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
(5, 'Lucia Kováčová', '+421900555555', 'lucia.kovacova@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
(6, 'Tomáš Malík', '+421900666666', 'tomas.malik@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE);


INSERT INTO Uzivatel (id, vek, pohlavie) VALUES
(1, 22, 'Muž'),
(2, 24, 'Žena'),
(5, 21, 'Žena'),
(6, 28, 'Muž');


INSERT INTO Moderator (id) VALUES (3),(4);


INSERT INTO Administrator (id) VALUES (4);


INSERT INTO Miesto (id, adresa, typ, popis, navrhol_id, stav, schvalil_id) VALUES
(1, 'Božetěchova 2, Brno', 'univerzita', 'Areál Fakulty informačních technologií VUT.', 1, 'schvalene', 3),
(2, 'Špilberk 210/1, Brno', 'verejne', 'Priestory vhodné pre kultúrne a spoločenské udalosti.', 2, 'schvalene', 3),
(3, 'Technická 12, Brno', 'univerzita', 'Navrhované miesto pre prednášky a študentské stretnutia.', 1, 'cakajuce', NULL);


INSERT INTO Kategorie (id, typ, rodic_id, navrhol_id, stav, schvalil_id) VALUES
(1, 'Vzdelávanie', NULL, 1, 'schvalene', 3),
(2, 'Hudba', NULL, 2, 'schvalene', 3),
(3, 'Komunita', NULL, 1, 'schvalene', 3),
(4, 'Prednáška', 1, 1, 'schvalene', 3),
(5, 'Seminár', 1, 1, 'schvalene', 3),
(6, 'Koncert', 2, 2, 'schvalene', 3),
(7, 'Meetup', 3, 1, 'schvalene', 3),
(8, 'Workshop', 1, 2, 'cakajuce', NULL);


INSERT INTO Udalost (
    id,
    oznacenie,
    datum_od,
    cas_od,
    datum_do,
    cas_do,
    vedenie_sedenia,
    kapacita,
    popis,
    ikona,
    miesto_id,
    kategoria_id,
    zakladatel_id,
    stav,
    schvalil_id
) VALUES
(
    1,
    'FIT-AI-2026',
    '2026-10-20',
    '17:00:00',
    '2026-10-20',
    '19:00:00',
    NULL,
    120,
    'Prednáška o využití umelej inteligencie v softvérovom vývoji.',
    '/images/ai-prednaska.jpg',
    1,
    4,
    1,
    'schvalene',
    3
),
(
    2,
    'BRNO-ROCK-2026',
    '2026-11-05',
    '19:00:00',
    '2026-11-05',
    '23:00:00',
    NULL,
    300,
    'Večerný koncert lokálnych hudobných skupín.',
    '/images/rock-koncert.jpg',
    2,
    6,
    2,
    'schvalene',
    3
),
(
    3,
    'DEV-MEETUP-2026',
    '2026-11-15',
    '18:00:00',
    '2026-11-15',
    '21:00:00',
    NULL,
    80,
    'Diskusia o nových technológiách.',
    '/images/dev-meetup.jpg',
    1,
    7,
    1,
    'cakajuce',
    NULL
),
(
    4,
    'WEB-SEMINAR-2026',
    '2026-09-15',
    '16:00:00',
    '2026-09-15',
    '18:00:00',
    NULL,
    60,
    'Seminár venovaný modernému webovému vývoju.',
    '/images/web-seminar.jpg',
    1,
    5,
    2,
    'schvalene',
    3
),
(
    5,
    'OPEN-DEV-MEETUP-2026',
    '2026-12-01',
    '18:00:00',
    '2026-12-01',
    '22:00:00',
    NULL,
    NULL,
    'Otvorené komunitné stretnutie programátorov bez obmedzenia kapacity.',
    '/images/open-meetup.jpg',
    1,
    7,
    5,
    'schvalene',
    4
);


INSERT INTO UdalostFoto (id, udalost_id, url) VALUES
(1, 1, '/uploads/events/ai-1.jpg'),
(2, 1, '/uploads/events/ai-2.jpg'),
(3, 2, '/uploads/events/rock-1.jpg'),
(4, 2, '/uploads/events/rock-2.jpg'),
(5, 4, '/uploads/events/web-1.jpg'),
(6, 5, '/uploads/events/meetup-1.jpg');


INSERT INTO MiestoFoto (id, miesto_id, url) VALUES
(1, 1, '/uploads/places/fit-1.jpg'),
(2, 1, '/uploads/places/fit-2.jpg'),
(3, 2, '/uploads/places/spilberk-1.jpg');


INSERT INTO Vstupne (id, cennik, typ, udalost_id) VALUES
(1, 10.00, 'Študent', 2),
(2, 18.00, 'Dospelý', 2),
(3, 35.00, 'VIP', 2),
(4, 5.00, 'Študent', 4),
(5, 10.00, 'Dospelý', 4);


INSERT INTO Registracia (id, uzivatel_id, udalost_id, vstupne_id) VALUES
(1, 1, 2, 1),
(2, 2, 2, 2),
(3, 1, 4, 4),
(4, 2, 4, 5),
(5, 5, 1, NULL),
(6, 5, 5, NULL);


INSERT INTO Zaplatil (id, registracia_id, zaplatene) VALUES
(1, 1, TRUE),
(2, 2, FALSE),
(3, 3, TRUE),
(4, 4, TRUE);


INSERT INTO Komentar (id, udalost_id, uzivatel_id, text, hodnotenie, moderoval_id) VALUES
(1, 4, 1, 'Seminár bol zaujímavý.', 5, NULL),
(2, 4, 2, 'Dobrá prednáška', 4, 3);