CREATE TABLE Account_User (
    user_id INT PRIMARY KEY,
    username VARCHAR(100) NOT NULL
);

CREATE TABLE League (
    league_id INT PRIMARY KEY,
    league_name VARCHAR(100) NOT NULL,
    season_year INT NOT NULL,
    user_id INT NOT NULL,

    CONSTRAINT League_User_fk
        FOREIGN KEY (user_id)
        REFERENCES Account_User(user_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_season_year
        CHECK (season_year >= 2000)
);

CREATE TABLE Team (
    team_id INT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    league_id INT NOT NULL,
    user_id INT NOT NULL,

    CONSTRAINT Team_league_fk
        FOREIGN KEY (league_id)
        REFERENCES League(league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT Team_user_fk
        FOREIGN KEY (user_id)
        REFERENCES Account_User(user_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    UNIQUE (team_id, league_id)
);

CREATE TABLE Player (
    player_id INT PRIMARY KEY,
    nfl_team VARCHAR(100) NOT NULL,
    jersey_number INT ,
    player_name VARCHAR(100) NOT NULL,
    player_position VARCHAR(5) NOT NULL,

    CONSTRAINT chk_player_position
        CHECK (player_position IN ('QB', 'RB', 'WR', 'TE', 'K', 'DEF')),

    CONSTRAINT chk_jersey_number
        CHECK (jersey_number BETWEEN 0 AND 99)
);

CREATE TABLE Matchup (
    matchup_number INT PRIMARY KEY,
    league_id INT NOT NULL,
    week_number INT NOT NULL,
    team_a_id INT NOT NULL,
    team_b_id INT NOT NULL,
    result VARCHAR(20),

    CONSTRAINT Matchup_League_fk
        FOREIGN KEY (league_id)
        REFERENCES League(league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT Matchup_Team_A_fk
        FOREIGN KEY (team_a_id)
        REFERENCES Team(team_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT Matchup_Team_B_fk
        FOREIGN KEY (team_b_id)
        REFERENCES Team(team_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT chk_matchup_week
        CHECK (week_number BETWEEN 1 AND 18),

    CONSTRAINT chk_different_teams
        CHECK (team_a_id <> team_b_id),

    CONSTRAINT chk_matchup_result
        CHECK (result IN ('TEAM_A', 'TEAM_B', 'TIE'))
);

CREATE TABLE League_Transaction (
    transaction_id INT PRIMARY KEY,
    transaction_date TIMESTAMP NOT NULL,
    transaction_type VARCHAR(100) NOT NULL,
    league_id INT NOT NULL,
    team_id INT NOT NULL,

    CONSTRAINT Transaction_league_fk
        FOREIGN KEY (league_id)
        REFERENCES League(league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT Transaction_team_fk
        FOREIGN KEY (team_id)
        REFERENCES Team(team_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    UNIQUE (transaction_id, league_id),

    CONSTRAINT chk_transaction_type
        CHECK (transaction_type IN ('ADD', 'DROP', 'TRADE'))
);

CREATE SEQUENCE transaction_line_number_seq
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE Transaction_Line (
    transaction_id INT NOT NULL,
    line_number INT NOT NULL
        DEFAULT nextval('transaction_line_number_seq'),
    player_id INT NOT NULL,
    league_id INT NOT NULL,
    from_team_id INT,
    to_team_id INT,

    PRIMARY KEY (transaction_id, line_number),

    CONSTRAINT fk_line_transaction
        FOREIGN KEY (transaction_id, league_id)
        REFERENCES League_Transaction(transaction_id, league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_line_player
        FOREIGN KEY (player_id)
        REFERENCES Player(player_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_line_from_team
        FOREIGN KEY (from_team_id, league_id)
        REFERENCES Team(team_id, league_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_line_to_team
        FOREIGN KEY (to_team_id, league_id)
        REFERENCES Team(team_id, league_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_line_number
        CHECK (line_number > 0),

    CONSTRAINT chk_different_transaction_teams
        CHECK (
            from_team_id IS NULL
            OR to_team_id IS NULL
            OR from_team_id <> to_team_id
        ),

    CONSTRAINT chk_transaction_has_team
        CHECK (
            from_team_id IS NOT NULL
            OR to_team_id IS NOT NULL
        )
);

CREATE TABLE Roster_Slot (
    slot_number INT PRIMARY KEY,
    slot_label VARCHAR(10) NOT NULL,
    week_number INT NOT NULL,
    team_id INT NOT NULL,
    player_id INT,

    CONSTRAINT Roster_Slot_team_fk
        FOREIGN KEY (team_id)
        REFERENCES Team(team_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT Roster_Slot_player_fk
        FOREIGN KEY (player_id)
        REFERENCES Player(player_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT chk_roster_week
        CHECK (week_number BETWEEN 1 AND 18),

    CONSTRAINT chk_slot_label
        CHECK (slot_label IN (
            'QB', 'RB1', 'RB2', 'WR1', 'WR2',
            'TE', 'FLEX', 'BENCH1', 'BENCH2',
            'BENCH3', 'BENCH4', 'BENCH5',
            'BENCH6', 'BENCH7'
        )),

    CONSTRAINT uq_roster_player_week
        UNIQUE (team_id, week_number, player_id)
);

CREATE TABLE Player_Game_Stat (
    season_year INT NOT NULL,
    week_number INT NOT NULL,
    player_id INT NOT NULL,
    fumbles_lost INT NOT NULL,
    receptions INT NOT NULL,
    rushing_tds INT NOT NULL,
    rushing_yards INT NOT NULL,
    passing_yards INT NOT NULL,
    passing_tds INT NOT NULL,
    receiving_tds INT NOT NULL,

    PRIMARY KEY (player_id, season_year, week_number),

    CONSTRAINT Player_Game_Stat_Player_fk
        FOREIGN KEY (player_id)
        REFERENCES Player(player_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT chk_stat_week
        CHECK (week_number BETWEEN 1 AND 18),

    CONSTRAINT chk_stat_season
        CHECK (season_year >= 2000),

    CONSTRAINT chk_stat_counts
        CHECK (
            fumbles_lost >= 0 AND
            receptions >= 0 AND
            rushing_tds >= 0 AND
            rushing_yards >= 0 AND
            receiving_tds >= 0 AND
            passing_yards >= 0 AND
            passing_tds >= 0
        )
);

INSERT INTO player (player_id, nfl_team, jersey_number, player_name, player_position)
VALUES 
('254','49ers','5','Mike Evans','WR'),
('122','Seahawks','11','Jaxon SMith-Njigba','WR'),
('111','Seahawks',NULL,'Seattle','DEF');

INSERT INTO account_user (user_id, username)
VALUES 
('0020','davidbeltran'),
('0021','markuspersson'),
('0022','janeausten');


INSERT INTO league (league_id, league_name, season_year, user_id)
VALUES
('9','Bennys League','2026','0020'),
('8','TDs and Fantasies','2026','0021'),
('7','Bengal Family','2026','0022');

INSERT INTO team (team_id, team_name, league_id, user_id)
Values
('901','Dangerous Dave','9','0020'),
('902','Notch','9','0021'),
('903','Pride and Prejudice','9','0022'),
('801','Notch','8','0021'),
('802','Dangerous Dave','8','0020'),
('701','Pride and Prejudice','7','0022'),
('702','Notch','7','0021');


INSERT INTO matchup (matchup_number, league_id, week_number, team_a_id, team_b_id)
VAlues
('01','9','1','901','902'),
('02','9','2','902','903'),
('03','9','3','903','901');



INSERT INTO league_transaction (transaction_id, transaction_date, transaction_type, league_id, team_id)
Values 
('1','2025-09-03 10:15:00','ADD','9','901'),
('2','2025-09-10 14:30:00','DROP','9','902'),
('3','2025-09-17 09:00:00','TRADE','9','901'),
('4','2025-09-24 18:45:00','ADD','9','903')




INSERT INTO transaction_line (transaction_id, player_id, league_id, from_team_id, to_team_id)
VALUES
('1','123','9',NULL,'901'),
('2','254','9','902',NULL),
('3','120','9','901','902'),
('3','121','9','902','901'),
('4','122','9',NULL,'903');



INSERT INTO roster_slot (slot_number, slot_label, week_number, team_id, player_id)
VALUES
('1','QB','1','901','123'),
('2','RB1','1','901','120'),
('3','RB2','1','901',NULL),
('4','BENCH1','1','901',NULL),
('5','TE','1','902','121'),
('6','WR1','1','902','254'),
('7','WR2','1','902',NULL),
('8','WR1','1','903','122'),
('9','FLEX','1','903',NULL);


INSERT INTO player_game_stat
(season_year, week_number, player_id, fumbles_lost, receptions, rushing_tds, rushing_yards, passing_yards, passing_tds, receiving_tds)
VALUES
('2025','1','123','1','0','0','8','245','2','0'),
('2025','1','121','0','6','0','0','0','0','1'),
('2025','1','120','0','3','1','78','0','0','0'),
('2025','1','254','0','7','0','0','0','0','1'),
('2025','1','122','1','9','0','0','0','0','0'),
('2025','1','111','0','0','0','0','0','0','0'),
('2025','2','123','0','0','0','5','198','1','0'),
('2025','2','121','0','4','0','0','0','0','0'),
('2025','2','120','0','2','0','64','0','0','0'),
('2025','2','254','1','5','0','0','0','0','0'),
('2025','2','122','0','8','0','0','0','0','1');