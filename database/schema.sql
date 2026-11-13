CREATE TABLE Account_User (
    user_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE League (
    league_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    league_name VARCHAR(100) NOT NULL,
    season_year INTEGER NOT NULL,
    user_id INTEGER NOT NULL,

    CONSTRAINT fk_league_user
        FOREIGN KEY (user_id)
        REFERENCES Account_User(user_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_season_year
        CHECK (season_year >= 2000)
);

CREATE TABLE Team (
    team_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    league_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,

    CONSTRAINT fk_team_league
        FOREIGN KEY (league_id)
        REFERENCES League(league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_team_user
        FOREIGN KEY (user_id)
        REFERENCES Account_User(user_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT uq_team_league_user
        UNIQUE (league_id, user_id),

    CONSTRAINT uq_team_id_league
        UNIQUE (team_id, league_id)
);

CREATE TABLE Player (
    player_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    player_name VARCHAR(100) NOT NULL,
    nfl_team VARCHAR(50) NOT NULL,
    jersey_number INTEGER,
    position VARCHAR(5) NOT NULL,

    CONSTRAINT chk_player_position
        CHECK (position IN ('QB', 'RB', 'WR', 'TE', 'K', 'DEF')),

    CONSTRAINT chk_jersey_number
        CHECK (jersey_number BETWEEN 0 AND 99)
);

CREATE TABLE Player_Game_Stat (
    player_id INTEGER NOT NULL,
    season_year INTEGER NOT NULL,
    week_number INTEGER NOT NULL,
    fantasy_points DECIMAL(7,2) NOT NULL DEFAULT 0,
    fumbles_lost INTEGER NOT NULL DEFAULT 0,
    receptions INTEGER NOT NULL DEFAULT 0,
    rushing_tds INTEGER NOT NULL DEFAULT 0,
    rushing_yrds INTEGER NOT NULL DEFAULT 0,
    receiving_tds INTEGER NOT NULL DEFAULT 0,
    passing_yards INTEGER NOT NULL DEFAULT 0,
    passing_tds INTEGER NOT NULL DEFAULT 0,

    CONSTRAINT pk_player_game_stat
        PRIMARY KEY (player_id, season_year, week_number),

    CONSTRAINT fk_stat_player
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
            rushing_yrds >= 0 AND
            receiving_tds >= 0 AND
            passing_yards >= 0 AND
            passing_tds >= 0
        )
);

CREATE TABLE Roster_slot (
    team_id INTEGER NOT NULL,
    week_number INTEGER NOT NULL,
    slot_label VARCHAR(10) NOT NULL,
    player_id INTEGER,

    CONSTRAINT pk_roster_slot
        PRIMARY KEY (team_id, week_number, slot_label),

    CONSTRAINT fk_roster_team
        FOREIGN KEY (team_id)
        REFERENCES Team(team_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_roster_player
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

CREATE TABLE Matchup (
    matchup_number INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    league_id INTEGER NOT NULL,
    week_number INTEGER NOT NULL,
    team_a_id INTEGER NOT NULL,
    team_b_id INTEGER NOT NULL,
    result VARCHAR(20),

    CONSTRAINT fk_matchup_league
        FOREIGN KEY (league_id)
        REFERENCES League(league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_matchup_team_a
        FOREIGN KEY (team_a_id, league_id)
        REFERENCES Team(team_id, league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_matchup_team_b
        FOREIGN KEY (team_b_id, league_id)
        REFERENCES Team(team_id, league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT chk_matchup_week
        CHECK (week_number BETWEEN 1 AND 18),

    CONSTRAINT chk_different_teams
        CHECK (team_a_id <> team_b_id),

    CONSTRAINT chk_matchup_result
        CHECK (result IN ('TEAM_A', 'TEAM_B', 'TIE'))
);

CREATE TABLE Transaction (
    transaction_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    transaction_type VARCHAR(10) NOT NULL,
    league_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,

    CONSTRAINT fk_transaction_league
        FOREIGN KEY (league_id)
        REFERENCES League(league_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_transaction_team
        FOREIGN KEY (team_id, league_id)
        REFERENCES Team(team_id, league_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT uq_transaction_id_league
        UNIQUE (transaction_id, league_id),

    CONSTRAINT chk_transaction_type
        CHECK (transaction_type IN ('ADD', 'DROP', 'TRADE'))
);

CREATE SEQUENCE transaction_line_number_seq
    START WITH 1
    INCREMENT BY 1;

CREATE TABLE transaction_line (
    transaction_id INTEGER NOT NULL,
    line_number INTEGER NOT NULL
        DEFAULT nextval('transaction_line_number_seq'),
    player_id INTEGER NOT NULL,
    league_id INTEGER NOT NULL,
    from_team_id INTEGER,
    to_team_id INTEGER,

    CONSTRAINT pk_transaction_line
        PRIMARY KEY (transaction_id, line_number),

    CONSTRAINT uq_transaction_player
        UNIQUE (transaction_id, player_id),

    CONSTRAINT fk_line_transaction
        FOREIGN KEY (transaction_id, league_id)
        REFERENCES Transaction(transaction_id, league_id)
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
