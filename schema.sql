CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    nickname VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) DEFAULT 'Dirt Game',
    num_rounds INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE game_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    player_id INT NOT NULL,
    player_order INT DEFAULT 0,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
);

CREATE TABLE rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    round_number INT NOT NULL,
    player_id INT NOT NULL,
    bid INT NOT NULL,
    hands_won INT NOT NULL,
    score INT NOT NULL,
    all_players_made_bid BOOLEAN DEFAULT FALSE,
    no_players_made_bid BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
);
