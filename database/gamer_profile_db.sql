-- ============================================================
-- Gamer Profile & Performance Analysis System
-- Database: gamer_profile_db
-- Import this file via phpMyAdmin > Import
-- ============================================================

CREATE DATABASE IF NOT EXISTS gamer_profile_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gamer_profile_db;

-- ============================================================
-- Drop tables in correct order (foreign keys first)
-- ============================================================
DROP TABLE IF EXISTS achievements;
DROP TABLE IF EXISTS statistics;
DROP TABLE IF EXISTS profiles;

-- ============================================================
-- Table: profiles
-- ============================================================
CREATE TABLE profiles (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    gamer_name    VARCHAR(100) NOT NULL,
    favorite_game VARCHAR(100) DEFAULT '',
    gamer_rank    VARCHAR(50)  DEFAULT 'Unranked',
    preferred_role VARCHAR(50) DEFAULT '',
    bio           TEXT,
    level         INT DEFAULT 1,
    xp            INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: statistics
-- ============================================================
CREATE TABLE statistics (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    profile_id     INT NOT NULL,
    game_title     VARCHAR(100) NOT NULL,
    kills          INT DEFAULT 0,
    deaths         INT DEFAULT 0,
    wins           INT DEFAULT 0,
    matches_played INT DEFAULT 0,
    accuracy       DECIMAL(5,2) DEFAULT 0.00,
    playtime       DECIMAL(8,2) DEFAULT 0.00,
    session_date   DATE,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table: achievements
-- ============================================================
CREATE TABLE achievements (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    profile_id  INT NOT NULL,
    badge_name  VARCHAR(100) NOT NULL,
    description TEXT,
    unlocked_at DATE,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Sample Data: profiles (5 rows minimum)
-- ============================================================
INSERT INTO profiles (gamer_name, favorite_game, gamer_rank, preferred_role, bio, level, xp) VALUES
('ShadowStrike',  'Call of Duty',  'Diamond',  'Sniper',        'Competitive FPS player with 5 years of experience. Specializes in long-range precision and map control.', 42, 8500),
('NeonViper',     'Valorant',      'Platinum',  'Duelist',       'Strategic player focused on team coordination and smart engagements. Always playing for the objective.', 35, 6200),
('CyberWolf',     'Apex Legends',  'Gold',      'Assault',       'Aggressive player who loves pushing enemies and creating chaos. High risk, high reward playstyle.', 28, 4800),
('PhantomAce',    'CS2',           'Master',    'Entry Fragger', 'High accuracy player and former semi-pro. Known for fast reaction times and clutch performances.', 55, 12000),
('BlazeFury',     'Fortnite',      'Silver',    'Builder',       'Creative builder and skilled shotgun user. Constantly improving mechanical skills and building techniques.', 18, 2100);

-- ============================================================
-- Sample Data: statistics (10 rows, at least 2 per profile)
-- ============================================================
INSERT INTO statistics (profile_id, game_title, kills, deaths, wins, matches_played, accuracy, playtime, session_date) VALUES
(1, 'Call of Duty',  350, 120,  45, 80, 72.50, 120.50, '2026-04-10'),
(1, 'Call of Duty',  280, 100,  38, 65, 68.00,  95.00, '2026-04-20'),
(2, 'Valorant',      210, 150,  30, 60, 65.20,  88.00, '2026-04-18'),
(2, 'Valorant',      195, 140,  28, 55, 62.00,  80.00, '2026-04-25'),
(3, 'Apex Legends',  185, 170,  20, 55, 58.40,  75.00, '2026-04-22'),
(3, 'Apex Legends',  200, 160,  25, 60, 60.50,  85.00, '2026-04-28'),
(4, 'CS2',           420,  90,  60, 85, 82.10, 150.00, '2026-04-25'),
(4, 'CS2',           380,  80,  55, 78, 80.50, 140.00, '2026-05-01'),
(5, 'Fortnite',       95, 120,  15, 50, 45.00,  60.00, '2026-04-28'),
(5, 'Fortnite',       88, 130,  12, 48, 42.00,  55.00, '2026-05-05');

-- ============================================================
-- Sample Data: achievements (8 rows)
-- ============================================================
INSERT INTO achievements (profile_id, badge_name, description, unlocked_at) VALUES
(1, 'First Win',     'Won your very first competitive match.',                  '2024-01-10'),
(1, '100 Kills',     'Achieved a total of 100 kills across all sessions.',       '2024-02-05'),
(1, 'Sharpshooter',  'Maintained over 70% accuracy for 5 consecutive sessions.','2024-06-01'),
(2, 'Team Leader',   'Won 10 matches while playing as team captain.',            '2024-03-12'),
(3, 'First Win',     'Won your very first competitive match.',                  '2024-01-20'),
(4, 'Sharpshooter',  'Maintained over 80% accuracy for 5 consecutive sessions.','2024-04-01'),
(4, 'Elite Player',  'Reached the Elite performance tier.',                     '2024-05-08'),
(5, 'First Win',     'Won your very first competitive match.',                  '2024-02-14');
