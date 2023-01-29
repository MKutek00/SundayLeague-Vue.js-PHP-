<?php

// namespace sunday_league;

// use PDO\PDOdb;
require __DIR__ . '/db.php';
require_once __DIR__ . '/../models/Team.php';
require_once __DIR__ . '/../models/League.php';




class sundayLeagueDAO
{

    const ADMIN = 1;
    const REDAKTOR = 2;
    const UZYTKOWNIK = 3;

    public static function test()
    {
        $connection = PDOdb::get();
        $query = "SELECT * FROM test";

        $statement = $connection->prepare($query);
        // $statement->bindValue('user_id', $user_id, \PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll();
        var_dump($result);
    }

    public static function addArticle($article, $author = 1)
    {
        $conn = PDOdb::get();
        $sql = "INSERT INTO sunday_league.articles (title, subtitle, text, photo, id_author) VALUES (:title, :subtitle, :text, :photo, :id_author)";
        $conn->prepare($sql)->execute(["title" => $article['title'], "subtitle" => $article['subtitle'], "text" => $article['text'], "photo" => $article['photo'], "id_author" => $author]);
        return $conn->lastInsertId();
    }

    public static function getArticles()
    {
        $conn = PDOdb::get();
        $sql = "SELECT a.id as id_article, a.title, a.subtitle, a.text, a.photo, a.id_author, a.date FROM sunday_league.articles a ORDER BY a.date DESC";
        $result = $conn->query($sql)->fetchAll();

        $articles = [];
        foreach ($result as &$article) {
            $stmt = $conn->prepare("SELECT u.login, u.avatar FROM sunday_league.users u WHERE u.id = :id");
            $stmt->execute(["id" => $article['id_author']]);
            $author = $stmt->fetch();

            $articles[] = ["id_article" => $article['id_article'], "author" => $author, "title" => $article['title'], "subtitle" => $article['subtitle'], "text" => $article['text'], "date" => $article['date'], "photo" => $article['photo']];
        }
        return $articles;
    }

    public static function getArticle($article_id)
    {
        $conn = PDOdb::get();
        $stmt = $conn->prepare("SELECT a.id as id_article, a.title, a.subtitle, a.`text`, a.photo, a.id_author, a.`date` FROM sunday_league.articles a WHERE a.id = :article");
        $stmt->execute(["article" => $article_id]);
        $article = $stmt->fetch();

        $stmtUser = $conn->prepare("SELECT u.login, u.avatar FROM sunday_league.users u WHERE u.id = :id");
        $stmtUser->execute(["id" => $article['id_author']]);
        $author = $stmtUser->fetch();

        $stmtComments = $conn->prepare("SELECT u.login as user_name, u.avatar as user_avatar,c.id, c.comment_text, c.date FROM sunday_league.comments c LEFT JOIN sunday_league.users u ON c.id_author = u.id WHERE c.id_article = :article");
        $stmtComments->execute(["article" => $article_id]);
        $comments = $stmtComments->fetchAll();

        $article['author'] = $author;
        $article['comments'] = $comments;
        unset($article['id_author']);

        return $article;
    }

    public static function addNewLeague($league)
    {
        $conn = PDOdb::get();
        $sql = "INSERT INTO sunday_league.leagues (id, league_name) VALUES (:league_id, :league_name)";
        $conn->prepare($sql)->execute(["league_id" => $league->getId(), "league_name" => $league->getName()]);
        return $conn->lastInsertId();
    }

    public static function addNewTeam($team)
    {
        $conn = PDOdb::get();
        $sql = "INSERT INTO sunday_league.teams (team_name, lat, `long`, `address`, id_league) VALUES (:team_name, :lat, :long, :team_address, :id_league)";
        $conn->prepare($sql)->execute(["team_name" => $team->getName(), "lat" => $team->getLat(), "long" => $team->getLng(), "team_address" => $team->getAddress(), "id_league" => $team->getLeagueId()]);
        return $conn->lastInsertId();
    }

    public static function getLeagues()
    {
        $conn = PDOdb::get();
        $sql = "SELECT l.id, l.league_name FROM sunday_league.leagues l";
        return $conn->query($sql)->fetchAll();
    }

    public static function getLeague($league_id)
    {
        $conn = PDOdb::get();
        $stmt = $conn->prepare("SELECT l.league_name, t.team_name, s.games, s.points, s.wins, s.draws, s.loses, s.goal_plus, s.goal_minus, s.goal_plus_minus
                FROM sunday_league.leagues l
                LEFT JOIN sunday_league.teams t ON l.id = t.id_league
                LEFT JOIN sunday_league.scoretable s ON t.id = s.id_team
                WHERE l.id = :league_id
                ORDER BY s.points, s.draws");
        $stmt->execute(["league_id" => $league_id]);
        return $stmt->fetch();
    }

    public static function getLeagueswithURL()
    {
        $conn = PDOdb::get();
        $sql = "SELECT id, league_name, scrap_url FROM sunday_league.leagues ORDER BY id";
        return $conn->query($sql)->fetchAll();
    }

    public static function getTeamsName($league_id)
    {
        $conn = PDOdb::get();
        $stmt = $conn->prepare("SELECT id, team_name FROM sunday_league.teams WHERE id_league = :league_id");
        $stmt->execute(["league_id" => $league_id]);
        $teams = $stmt->fetchAll();
        foreach ($teams as $team) {
            $trimmedName = str_replace(" ", "", $team['team_name']);
            $result[$trimmedName] = $team['id'];
        }

        return $result;
    }

    public static function addSchedule($teamAId, $teamAGoals, $teamBId, $teamBGoals, $date, $kolejka)
    {
        $conn = PDOdb::get();
        $stmt = $conn->prepare("INSERT INTO sunday_league.schedule (team_a, team_b, goals_a, goals_b, `date`, kolejka) 
                                VALUES (:team_a, :team_b, :goals_a, :goals_b, :date, :kolejka)
                                ON DUPLICATE KEY UPDATE goals_a = :goals_a, goals_b = :goals_b");
        $stmt->bindParam("team_a", $teamAId);
        $stmt->bindParam("team_b", $teamBId);
        $stmt->bindParam("goals_a", $teamAGoals);
        $stmt->bindParam("goals_b", $teamBGoals);
        $stmt->bindParam("date", $date);
        $stmt->bindParam("kolejka", $kolejka);
        $stmt->execute();
    }

    public static function generateTable()
    {
        $conn = PDOdb::get();

        $leagues = self::getLeagues();
        foreach ($leagues as $league) {
            $stmt = $conn->prepare(
                "SELECT
                A.id,
                A.team_name,
                (COALESCE(A.games_played,     0) + COALESCE(B.games_played,     0)) AS games_played,
                (COALESCE(A.points,           0) + COALESCE(B.points,           0)) AS points,
                (COALESCE(A.wins,             0) + COALESCE(B.wins,             0)) AS wins,
                (COALESCE(A.draws,            0) + COALESCE(B.draws,            0)) AS draws,
                (COALESCE(A.loses,            0) + COALESCE(B.loses,            0)) AS loses,
                (COALESCE(A.goals_scored,     0) + COALESCE(B.goals_scored,     0)) AS goals_scored,
                (COALESCE(A.goals_lost,       0) + COALESCE(B.goals_lost,       0)) AS goals_lost,
                (COALESCE(A.goals_plus_minus, 0) + COALESCE(B.goals_plus_minus, 0)) AS goals_plus_minus
              FROM
                (SELECT 
                  t.id,
                  t.team_name,
                  COUNT(*) games_played,
                  SUM(CASE 
                          WHEN s.goals_a > s.goals_b THEN 3
                          WHEN s.goals_a = s.goals_b THEN 1
                          ELSE 0
                      END) AS points,
                  SUM(s.goals_a > s.goals_b) AS wins, 
                  SUM(s.goals_a = s.goals_b) AS draws, 
                  SUM(s.goals_a < s.goals_b) AS loses,
                  SUM(s.goals_a) AS goals_scored, 
                  SUM(s.goals_b) AS goals_lost,
                  SUM(s.goals_a - s.goals_b) AS goals_plus_minus
              FROM sunday_league.`schedule` s
              LEFT JOIN sunday_league.teams t ON s.team_a = t.id
              LEFT JOIN sunday_league.leagues l ON t.id_league = l.id
              WHERE l.id = :league_id AND s.goals_a IS NOT NULL AND s.goals_b IS NOT NULL
              GROUP BY t.id
              ) AS A
              LEFT JOIN
                (SELECT 
                  t.id,
                  t.team_name,
                  COUNT(*) games_played,
                  SUM(CASE 
                          WHEN s.goals_a < s.goals_b THEN 3
                          WHEN s.goals_a = s.goals_b THEN 1
                          ELSE 0
                      END) AS points,
                  SUM(s.goals_a < s.goals_b) AS wins, 
                  SUM(s.goals_a = s.goals_b) AS draws, 
                  SUM(s.goals_a > s.goals_b) AS loses,
                  SUM(s.goals_b) AS goals_scored, 
                  SUM(s.goals_a) AS goals_lost,
                  SUM(s.goals_b - s.goals_a) AS goals_plus_minus
              FROM sunday_league.`schedule` s
              LEFT JOIN sunday_league.teams t ON s.team_b = t.id
              LEFT JOIN sunday_league.leagues l ON t.id_league = l.id
              WHERE l.id = :league_id AND s.goals_a IS NOT NULL AND s.goals_b IS NOT NULL
              GROUP BY t.id
              ) AS B
              ON
                A.team_name = B.team_name
                ORDER BY points DESC, wins DESC"
            );
            $stmt->execute(["league_id" => $league['id']]);
            $result = $stmt->fetchAll();

            foreach ($result as $team) {
                self::addTeamToScoreTable($team);
            }
        }
    }

    public static function addTeamToScoreTable($team)
    {
        $conn = PDOdb::get();


        $sql = "INSERT INTO sunday_league.scoretable (id_team, games, points, wins, draws, loses, goal_plus, goal_minus, goal_plus_minus) VALUES (:id_team, :games, :points, :wins, :draws, :loses, :goal_plus, :goal_minus, :goal_plus_minus) ON DUPLICATE KEY UPDATE id_team = :id_team, games = :games, points = :points, wins = :wins, draws = :draws, loses = :loses, goal_plus = :goal_plus, goal_minus = :goal_minus, goal_plus_minus = :goal_plus_minus";
        $conn->prepare($sql)->execute(["id_team" => $team['id'], "games" => $team['games_played'], "points" => $team['points'], "wins" => $team['wins'], "draws" => $team['draws'], "loses" => $team['loses'], "goal_plus" => $team['goals_scored'], "goal_minus" => $team['goals_lost'], "goal_plus_minus" => $team['goals_plus_minus']]);
    }

    public static function getGameOnDate($date)
    {
        $conn = PDOdb::get();
        $stmt = $conn->prepare("
            SELECT s.id, l.league_name, t1.team_name AS team_a,s.goals_a, t1.lat AS lat_a, t1.`long` AS long_a, t2.team_name AS team_b,s.goals_b, t1.address adrdress_a, s.`date` FROM `schedule` s 
            LEFT JOIN teams t1 ON s.team_a = t1.id
            LEFT JOIN teams t2 ON s.team_b = t2.id            
            LEFT JOIN leagues l ON t1.id_league = l.id
            WHERE DATE(s.`date`) = :date
        ");
        $stmt->execute(["date" => $date]);

        return $stmt->fetchAll();
    }

    public static function getTeamTable($league)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
        SELECT t.team_name, s.games, s.points, s.wins, s.draws, s.loses, s.goal_plus, s.goal_minus, s.goal_plus_minus
            FROM sunday_league.leagues l
            LEFT JOIN sunday_league.teams t ON l.id = t.id_league
            LEFT JOIN sunday_league.scoretable s ON s.id_team = t.id
            WHERE l.league_name = :league_name
            ORDER BY s.points DESC, s.wins DESC, s.goal_plus DESC
        ");
        $stmt->execute(["league_name" => $league]);
        return $stmt->fetchAll();
    }

    public static function getSchedule($league)
    {
        $conn = PDOdb::get();
        $stmt = $conn->prepare("
        SELECT s.id, t1.team_name AS team_a, t2.team_name AS team_b, s.goals_a, s.goals_b, s.`date`,s.kolejka, t1.lat AS lat_a, t1.`long` as long_a 
            FROM sunday_league.`schedule` s
            LEFT JOIN sunday_league.teams t1 ON t1.id = s.team_a
            LEFT JOIN sunday_league.teams t2 ON t2.id = s.team_b
            LEFT JOIN sunday_league.leagues l ON l.id = t1.id_league AND l.id = t2.id_league
            WHERE l.league_name = :league_name
            ORDER BY s.kolejka
        ");
        $stmt->execute(["league_name" => $league]);

        return $stmt->fetchAll();
    }
    public static function updateSchedule($schedule)
    {
        $conn = PDOdb::get();

        $sql = "UPDATE sunday_league.`schedule` s SET s.goals_a = :goals_a, s.goals_b = :goals_b  WHERE s.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["id" => $schedule['id'], "goals_a" => $schedule['goals_a'], "goals_b" => $schedule['goals_b']]);
        self::generateTable();
    }

    public static function registerUser($user)
    {
        $conn = PDOdb::get();
        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO sunday_league.users (`login`, `password`, `email`) VALUES (:login, :password, :email)";
        $conn->prepare($sql)->execute(["login" => $user['login'], "password" => $user['password'], "email" => $user['email']]);
        return $conn->lastInsertId();
    }

    public static function checkIdenticalLogin($login)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
        SELECT u.id
            FROM sunday_league.users u
            WHERE u.login = :login
        ");
        $stmt->execute(["login" => $login]);

        return $stmt->fetchColumn() > 0;
    }

    public static function checkIdenticalEmail($email)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
        SELECT u.id
            FROM sunday_league.users u
            WHERE u.email = :email
        ");
        $stmt->execute(["email" => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public static function loginUser($id)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
            SELECT u.id as id_user, u.login, u.email, u.avatar, u.id_role FROM sunday_league.users u
            WHERE u.id = :id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetch();
    }
    public static function hashedPassword($login)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
        SELECT u.id, u.password FROM sunday_league.users u
        WHERE u.login = :login");
        $stmt->execute(["login" => $login]);
        return $stmt->fetch();
    }
    public static function hashedPassword2($id)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
        SELECT u.password FROM sunday_league.users u
        WHERE u.id = :id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch();
    }
    public static function getUserRole($user)
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
            SELECT r.id as id_role, r.role_name FROM sunday_league.roles r
            WHERE r.id = :id");
        $stmt->execute(["id" => $user['id_role']]);
        $role = $stmt->fetch();

        $user['role'] = $role;
        unset($user['id_role']);

        return $user;
    }

    public static function getUsers()
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("
            SELECT u.id as id_user, u.login, u.email,u.avatar, u.id_role, r.id, r.role_name FROM sunday_league.users u
            LEFT JOIN sunday_league.roles r ON u.id_role = r.id  
        ");
        $stmt->execute();
        $users = $stmt->fetchAll();

        foreach ($users as $user) {
            $result[] = [
                "id_user" => $user['id_user'],
                "login" => $user['login'],
                "email" => $user['email'],
                "avatar" => $user['avatar'],
                "role" => [
                    "id_role" => $user['id'],
                    "role_name" => $user['role_name']
                ],
            ];
        }
        return $result;
    }

    public static function getRoles()
    {
        $conn = PDOdb::get();

        $stmt = $conn->prepare("SELECT r.id as id_role, r.role_name FROM sunday_league.roles r");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function updateUser($user)
    {
        $conn = PdoDb::get();

        $sql = "UPDATE sunday_league.users u SET u.id_role = :id_role WHERE u.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["id_role" => $user['role']['id_role'], "id" => $user['id_user']]);
    }

    public static function addCommentToArticle($article, $comment, $user)
    {

        $conn = PDOdb::get();
        $sql = "INSERT INTO sunday_league.comments (comment_text, id_author, id_article) VALUES (:comment_text, :id_author, :id_article)";
        $conn->prepare($sql)->execute(["comment_text" => $comment, "id_author" => $user, "id_article" => $article]);
        $insertedComment = $conn->lastInsertId();

        $newComment = $conn->prepare("SELECT u.login as user_name, u.avatar as user_avatar, c.comment_text, c.date FROM sunday_league.comments c LEFT JOIN sunday_league.users u ON c.id_author = u.id WHERE c.id = :id");
        $newComment->execute(["id" => $insertedComment]);
        return $newComment->fetch();
    }

    public static function updateUserPassowrd($id, $newPassword)
    {
        $conn = PDOdb::get();

        $sql = "UPDATE sunday_league.users u SET u.password = :newPassword WHERE u.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["newPassword" => $newPassword, "id" => $id]);

        return $stmt->rowCount();
    }

    public static function updateAvatar($id, $newAvatar)
    {
        $conn = PDOdb::get();
        $sql = "UPDATE sunday_league.users u SET u.avatar = :newAvatar WHERE u.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["newAvatar" => $newAvatar, "id" => $id]);
    }

    public static function deleteComment($id)
    {
        $conn = PdoDb::get();
        $sql = "DELETE FROM sunday_league.comments c WHERE c.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["id" => $id]);
    }
    public static function deleteArticle($id)
    {
        $conn = PdoDb::get();
        $sql = "DELETE FROM sunday_league.articles a WHERE a.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["id" => $id]);
    }

    public static function deleteUser($id)
    {
        $conn = PdoDb::get();
        $sql = "DELETE FROM sunday_league.users u WHERE u.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["id" => $id]);
    }

    public static function updateArticle($article)
    {
        $conn = PdoDb::get();
        $sql = "UPDATE sunday_league.articles a SET a.subtitle = :subtitle, a.`text` = :tekst WHERE a.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(["subtitle" => $article['subtitle'], "tekst" => $article['text'], "id" => $article['id_article']]);
    }
}
