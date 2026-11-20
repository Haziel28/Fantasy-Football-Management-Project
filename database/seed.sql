BEGIN;

INSERT INTO player (player_id, nfl_team, jersey_number, player_name, player_position) VALUES
(254, '49ers', 5, 'Mike Evans', 'WR'),
(122, 'Seahawks', 11, 'Jaxon Smith-Njigba', 'WR'),
(111, 'Seahawks', NULL, 'Seattle', 'DEF'),
-- Placeholder records for IDs referenced by the original sample transactions/rosters/stats.
(123, 'Sample Team', NULL, 'Sample Quarterback', 'QB'),
(120, 'Sample Team', NULL, 'Sample Running Back', 'RB'),
(121, 'Sample Team', NULL, 'Sample Tight End', 'TE');

INSERT INTO account_user (user_id, username) VALUES
(20, 'davidbeltran'),
(21, 'markuspersson'),
(22, 'janeausten');

INSERT INTO league (league_id, league_name, season_year, user_id) VALUES
(9, 'Bennys League', 2026, 20),
(8, 'TDs and Fantasies', 2026, 21),
(7, 'Bengal Family', 2026, 22);

INSERT INTO team (team_id, team_name, league_id, user_id) VALUES
(901, 'Dangerous Dave', 9, 20),
(902, 'Notch', 9, 21),
(903, 'Pride and Prejudice', 9, 22),
(801, 'Notch', 8, 21),
(802, 'Dangerous Dave', 8, 20),
(701, 'Pride and Prejudice', 7, 22),
(702, 'Notch', 7, 21);

INSERT INTO matchup (matchup_number, league_id, week_number, team_a_id, team_b_id) VALUES
(1, 9, 1, 901, 902),
(2, 9, 2, 902, 903),
(3, 9, 3, 903, 901);

INSERT INTO league_transaction (transaction_id, transaction_date, transaction_type, league_id, team_id) VALUES
(1, '2025-09-03 10:15:00', 'ADD', 9, 901),
(2, '2025-09-10 14:30:00', 'DROP', 9, 902),
(3, '2025-09-17 09:00:00', 'TRADE', 9, 901),
(4, '2025-09-24 18:45:00', 'ADD', 9, 903);

INSERT INTO transaction_line (transaction_id, player_id, league_id, from_team_id, to_team_id) VALUES
(1, 123, 9, NULL, 901),
(2, 254, 9, 902, NULL),
(3, 120, 9, 901, 902),
(3, 121, 9, 902, 901),
(4, 122, 9, NULL, 903);

INSERT INTO roster_slot (slot_number, slot_label, week_number, team_id, player_id) VALUES
(1, 'QB', 1, 901, 123),
(2, 'RB1', 1, 901, 120),
(3, 'RB2', 1, 901, NULL),
(4, 'BENCH1', 1, 901, NULL),
(5, 'TE', 1, 902, 121),
(6, 'WR1', 1, 902, 254),
(7, 'WR2', 1, 902, NULL),
(8, 'WR1', 1, 903, 122),
(9, 'FLEX', 1, 903, NULL);

-- Adjusted from 2025 to 2026 to match the seeded leagues.
INSERT INTO player_game_stat
(season_year, week_number, player_id, fumbles_lost, receptions, rushing_tds, rushing_yards, passing_yards, passing_tds, receiving_tds)
VALUES
(2026, 1, 123, 1, 0, 0, 8, 245, 2, 0),
(2026, 1, 121, 0, 6, 0, 0, 0, 0, 1),
(2026, 1, 120, 0, 3, 1, 78, 0, 0, 0),
(2026, 1, 254, 0, 7, 0, 0, 0, 0, 1),
(2026, 1, 122, 1, 9, 0, 0, 0, 0, 0),
(2026, 1, 111, 0, 0, 0, 0, 0, 0, 0),
(2026, 2, 123, 0, 0, 0, 5, 198, 1, 0),
(2026, 2, 121, 0, 4, 0, 0, 0, 0, 0),
(2026, 2, 120, 0, 2, 0, 64, 0, 0, 0),
(2026, 2, 254, 1, 5, 0, 0, 0, 0, 0),
(2026, 2, 122, 0, 8, 0, 0, 0, 0, 1);

COMMIT;