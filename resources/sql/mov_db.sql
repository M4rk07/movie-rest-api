SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET GLOBAL time_zone = "+01:00";

--
-- Database: `mov_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `mov_user`
--

CREATE TABLE IF NOT EXISTS mov_user (
  user_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  email varchar(50) CHARACTER SET utf8 NOT NULL,
  first_name varchar(50) CHARACTER SET utf8 NOT NULL,
  last_name varchar(50) CHARACTER SET utf8 NOT NULL,
  full_name varchar(100) CHARACTER SET utf8 NOT NULL,
  date_registered TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(user_id),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mov_movie`
--

CREATE TABLE IF NOT EXISTS mov_movie (
  movie_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  movie_name varchar(50) CHARACTER SET utf8 NOT NULL,
  cover_url varchar(50) CHARACTER SET utf8 NOT NULL,
  genre varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY(movie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mov_rate`
--

CREATE TABLE IF NOT EXISTS mov_rate (
  user_id int(11) UNSIGNED NOT NULL,
  movie_id int(11) UNSIGNED NOT NULL,
  rate int(11) UNSIGNED NOT NULL,
  comment varchar(3000) CHARACTER SET utf8 DEFAULT '',
  PRIMARY KEY(user_id, movie_id),
  FOREIGN KEY(user_id) REFERENCES mov_user(user_id),
  FOREIGN KEY(movie_id) REFERENCES mov_movie(movie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_user`
--

CREATE TABLE IF NOT EXISTS oauth2_user (
  user_id int(11) NOT NULL,
  username varchar(50) CHARACTER SET utf8 NOT NULL,
  password varchar(128) CHARACTER SET utf8 NOT NULL,
  salt varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY (username),
  FOREIGN KEY (username) REFERENCES mov_user(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_clients`
--

CREATE TABLE IF NOT EXISTS oauth2_client (
  client_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  client_secret varchar(255) NOT NULL,
  app_name varchar(100) NOT NULL,
  redirect_uri text,
  registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  client_type varchar(12) NOT NULL DEFAULT 'public',
  PRIMARY KEY (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_access_tokens`
--

CREATE TABLE IF NOT EXISTS oauth2_access_token (
  access_token varchar(32) NOT NULL,
  token_type varchar(20) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, access_token),
  FOREIGN KEY (client_id) REFERENCES oauth2_client(client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_refresh_token`
--

CREATE TABLE IF NOT EXISTS oauth2_refresh_token (
  refresh_token varchar(32) NOT NULL,
  client_id int(11) UNSIGNED NOT NULL,
  username varchar(50) NOT NULL,
  expires TIMESTAMP NOT NULL,
  date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  scope text,
  PRIMARY KEY (username, refresh_token),
  FOREIGN KEY (client_id) REFERENCES oauth2_client(client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- POPULATE table `oauth2_client`
--

INSERT INTO oauth2_client (app_name) VALUES ('Movies');

-- --------------------------------------------------------

--
-- POPULATE table `mov_movie`
--

INSERT INTO mov_movie (movie_name, genre, cover_url) VALUES
('Split', 'Thriller', 'https://images-na.ssl-images-amazon.com/images/M/MV5BZTJiNGM2NjItNDRiYy00ZjY0LTgwNTItZDBmZGRlODQ4YThkL2ltYWdlXkEyXkFqcGdeQXVyMjY5ODI4NDk@._V1_UX182_CR0,0,182,268_AL_.jpg'),
('Avengers: Age of Ultron', 'Action', 'https://images-na.ssl-images-amazon.com/images/M/MV5BMTM4OGJmNWMtOTM4Ni00NTE3LTg3MDItZmQxYjc4N2JhNmUxXkEyXkFqcGdeQXVyNTgzMDMzMTg@._V1_UX182_CR0,0,182,268_AL_.jpg'),
('Arrival', 'Sci-Fi', 'https://images-na.ssl-images-amazon.com/images/M/MV5BMTExMzU0ODcxNDheQTJeQWpwZ15BbWU4MDE1OTI4MzAy._V1_UX182_CR0,0,182,268_AL_.jpg'),
('Doctor Strange', 'Adventure', 'https://images-na.ssl-images-amazon.com/images/M/MV5BNTA2NTg0MjM0OV5BMl5BanBnXkFtZTgwNTEyMDExMTI@._V1_UY268_CR6,0,182,268_AL_.jpg');