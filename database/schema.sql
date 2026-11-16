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
    jersey_number INT NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    position VARCHAR(5) NOT NULL,

    CONSTRAINT chk_player_position
        CHECK (position IN ('QB', 'RB', 'WR', 'TE', 'K', 'DEF')),

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
