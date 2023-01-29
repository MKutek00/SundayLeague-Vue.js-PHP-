<?php
$_POST = json_decode(file_get_contents("php://input"), true);

// namespace sunday_league;
require __DIR__ . '/../helpers/FireBase/BeforeValidException.php';
require __DIR__ . '/../helpers/FireBase/CachedKeySet.php';
require __DIR__ . '/../helpers/FireBase/ExpiredException.php';
require __DIR__ . '/../helpers/FireBase/SignatureInvalidException.php';
require __DIR__ . '/../helpers/FireBase/JWK.php';
require __DIR__ . '/../helpers/FireBase/JWT.php';
require __DIR__ . '/../helpers/FireBase/Key.php';
require __DIR__ . '/../helpers/Json.php';
require __DIR__ . '/../dao/sundayLeagueDAO.php';

require_once __DIR__ . '/../models/Team.php';
require_once __DIR__ . '/../models/League.php';
require __DIR__ . '/../helpers/simple_html_dom.php';

require_once 'config.php';

use Helper\Json;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class sundayLeague
{
    private $request;
    private $bearer;
    private $admin;
    private $redaktor;
    private $uzytkownik;


    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];
        $this->bearer = trim(str_replace('Bearer', '', $_SERVER['HTTP_AUTHORIZATION']));
        $this->admin = sundayLeagueDAO::ADMIN;
        $this->redaktor = sundayLeagueDAO::REDAKTOR;
        $this->uzytkownik = sundayLeagueDAO::UZYTKOWNIK;
    }

    protected function authenticate($id_role)
    {
        $jwt = $this->bearer;
        $jwt = $jwt ? JWT::decode($jwt, new Key(PUBLICKEY, ALGORITHM)) : false;
        $function = trim($_SERVER['REQUEST_URI'], '/');
        if ($function != 'loginUser') {
            if ($jwt->exp < time()) {
                die("Brak aktywnej sesji");
            }
            if (is_array($id_role)) {
                if (!in_array($jwt->user->role->id_role, [1, 2, 3])) {
                    die("Brak uprawnień sesji");
                }
            } else {
                if ($jwt->user->role->id_role != $id_role) {
                    die("Brak uprawnień sesji");
                }
            }
        }
    }

    protected function isGet(): bool
    {
        return $this->request === 'GET';
    }

    protected function isPost(): bool
    {
        return $this->request === 'POST';
    }

    public function addArticle()
    {
        self::authenticate([$this->admin, $this->redaktor]);

        $inserted_id = sundayLeagueDAO::addArticle($_POST['article'], $_POST['user_id']);

        echo Json::toJson($inserted_id);
        return;
    }

    public function getArticles()
    {
        $articles = sundayLeagueDAO::getArticles();
        echo Json::toJson($articles);
        return;
    }

    public function getArticle()
    {
        $articleId = $_POST['article'];
        $article = sundayLeagueDAO::getArticle($articleId);
        echo Json::toJson($article);
        return;
    }

    public function scrapTeamsAndLeagues()
    {
        $host = 'https://regiowyniki.pl/';
        $pages = PAGES;
        foreach ($pages as $it => $page) {
            if ($it == 4) {

                $leagueId = $it;
                $leagueName = (explode("/", $page));
                $leagueName = implode(" ", array_slice($leagueName, count($leagueName) - 2));
                $league = new League($leagueId, $leagueName);
                // sundayLeagueDAO::addNewLeague($league);
                // echo $league->getId() . " - " . $league->getName() . "<br>";
                $html = file_get_html($page);

                $teamLine = $html->find('.name');

                // Na stronie są 4 identyczne tabele, więc liczbę linii ddzielimy przez 4
                $counter = count($teamLine) / 4;

                foreach ($teamLine as $key => $team) {
                    if ($key == $counter) break;

                    // Pobieramy HTML z informacji dt. wybranej Drużyny
                    $newUrl = file_get_html($host . $team->find('a', 0)->href);

                    // Pobieramy nazwe wybranej Drużyny
                    $teamNameDiv = $newUrl->find('.teamname', 0);
                    $teamName = $teamNameDiv->find('h1', 0)->plaintext;

                    // Pobieramy adres wybranej Drużyny
                    $detailsDiv = $newUrl->find('.details', 0);
                    $teamAddress = $detailsDiv->find('div .col-md-9', 2)->plaintext;

                    // Jeśli nie podano adresu to jako adres podajemy nazwę, by Geocode spróbował znaleźć sam adres
                    if (is_numeric(strpos($teamAddress, 'brak')))  $teamAddress = $teamName;

                    $team = new Team($teamName, $teamAddress, $leagueId);
                    // sundayLeagueDAO::addNewTeam($team);
                    echo $team->getLeagueId() . " - " . $team->getName() . " = " . $team->getAddress() . " - " . $team->getLat() . " - " . $team->getLng() . " <br />";
                }
            }
        }
    }

    public function getLeagues()
    {
        $leagues = sundayLeagueDAO::getLeagues();
        $result = [];
        foreach ($leagues as $league) {
            $league_name = explode(' ', $league['league_name']);

            $result[$league_name[0]][] = $league;
        }
        ksort($result);
        echo Json::toJson($result);
        return;
    }

    public function getLeague()
    {
        $league_id = $_POST['league_id'];

        $teams = sundayLeagueDAO::getLeague($league_id);
        echo Json::toJson($teams);
        return;
    }


    public function scrapSchedule()
    {
        $leagues = sundayLeagueDAO::getLeagueswithURL();

        foreach ($leagues as $league) {

            if ($league['id'] != 1 && $league['id'] < 10) {
                echo "League: " . $league['league_name'] . "<br>";
                $teams = sundayLeagueDAO::getTeamsName($league['id']);

                $html = file_get_html($league['scrap_url']);
                $maxNum = count($html->find('table.main')) - 2;
                $temp = 0;

                $allRounds = $html->find('table.main');

                foreach ($allRounds as $it => $round) {
                    if ($it == $maxNum) break;

                    if ($temp % 2) {
                        $rows = $round->find('tr');

                        foreach ($rows as $row) {


                            $tds = $row->find('td');
                            if (count($tds) == 4) {

                                $match = [''];
                                $teamAName = str_replace(" ", "", $tds[0]->plaintext);
                                preg_match("/\([^\]]*\)/", $teamAName, $match);
                                $teamAName = str_replace($match[0], "", $teamAName);

                                $match = [''];
                                $teamBName = str_replace(" ", "", $tds[2]->plaintext);
                                preg_match("/\([^\]]*\)/", $teamBName, $match);
                                $teamBName = str_replace($match[0], "", $teamBName);


                                $teamAId = $teams[$teamAName];
                                $teamBId = $teams[$teamBName];

                                $teamResult = explode("-", $tds[1]->plaintext);

                                $teamAGoals = $teamResult[0] == "" ? null : $teamResult[0];
                                $teamBGoals = $teamResult[1] == "" ? null : $teamResult[1];

                                $date = str_replace(",", "", $tds[3]->plaintext);
                                $rok = date("Y");
                                $daty = [
                                    "sierpnia" => ["msc" => '08', "rok" => $rok],
                                    "września" => ["msc" => '09', "rok" => $rok],
                                    "października" => ["msc" => '10', "rok" => $rok],
                                    "listopada" => ["msc" => '11', "rok" => $rok],
                                    "grudnia" => ["msc" => '12', "rok" => $rok],
                                    "stycznia" => ["msc" => '01', "rok" => $rok - 1],
                                    "lutego" => ["msc" => '02', "rok" => $rok - 1],
                                    "marca" => ["msc" => '03', "rok" => $rok - 1],
                                    "kwietnia" => ["msc" => '04', "rok" => $rok - 1],
                                    "maja" => ["msc" => '05', "rok" => $rok - 1],
                                    "czerwca" => ["msc" => '06', "rok" => $rok - 1],
                                    "lipca" => ["msc" => '07', "rok" => $rok - 1],

                                ];
                                if ($date) {

                                    $date = explode(" ", $date);
                                    $decDate = $daty[$date[1]];
                                    $date = $decDate['rok'] . "-" . $decDate['msc'] . "-" . $date[0] . " " . $date[2] . ":00";
                                } else {
                                    $date = null;
                                }
                                if ($teamAId != null && $teamBId != null) {
                                    $kolejka = ($temp + 1) / 2;
                                    // sundayLeagueDAO::addSchedule($teamAId, $teamAGoals, $teamBId, $teamBGoals, $date, $kolejka);
                                    echo "Kolejk: " . $kolejka . " Team A: <b>" . $teamAId . "</b> Team Bid: <b>" . $teamBId . "</b> Data: <b>" . $date . "</b> Bramki A: <b>" . $teamAGoals . "</b> Bramki B: <b>" . $teamBGoals . "</b> <br>";
                                }
                            }
                        }
                    }

                    $temp++;
                }
            }
        }
        sundayLeagueDAO::generateTable();
    }

    public function getCloseGames()
    {
        $date = $_POST['location']['date'];
        $coords = self::geocode($_POST['location']['address']);
        $lat = $coords[0];
        $long = $coords[1];
        $radius = $_POST['location']['radius'];
        $games = sundayLeagueDAO::getGameOnDate($date);

        foreach ($games as &$game) {
            $distance = self::haversineGreatCircleDistance($lat, $long, $game['lat_a'], $game['long_a']);
            if ($distance < $radius) {
                $game['distance'] = $distance;
                $result[] = $game;
            }
        }
        echo Json::toJson($result);
        return;
    }

    private function geocode($address)
    {

        // url encode the address
        $address = urlencode($address);

        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);
        // response status will be 'OK', if able to geocode given address
        if ($resp['status'] == 'OK') {

            // get the important data
            $lati = $resp['results'][0]['geometry']['location']['lat'] ?? "";
            $longi = $resp['results'][0]['geometry']['location']['lng'] ?? "";
            // verify if data is complete
            if ($lati && $longi) {
                // put the data in the array
                $data_arr = array();
                array_push($data_arr, $lati, $longi,);

                return $data_arr;
            }
        }
    }

    private function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371
    ) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function getTeamTable()
    {
        $league_name = $_POST['league_name'];
        $table = sundayLeagueDAO::getTeamTable($league_name);
        echo Json::toJson($table);
        return;
    }

    public function getLeagueSchedule()
    {
        $league_name = $_POST['league_name'];
        $schedule  = sundayLeagueDAO::getSchedule($league_name);

        foreach ($schedule as $row) {
            $result[$row['kolejka']][] = $row;
        }
        echo Json::toJson($result);
        return;
    }

    public function registerUser()
    {
        $newUser = $_POST['user'];
        if ($newUser['login'] == null || $newUser['email'] == null || $newUser['password'] == null) {
            throw new Exception("Podano niepełne dane");
        }
        if (sundayLeagueDAO::checkIdenticalLogin($newUser['login'])) {
            throw new Exception("Użytwkonik z podanym loginem już istnieje");
        }
        if (sundayLeagueDAO::checkIdenticalEmail($newUser['email'])) {
            throw new Exception("Użytwkonik z podanym emailem już istnieje");
        }
        sundayLeagueDAO::registerUser($newUser);
        echo Json::toJson("Success");
        return;
    }

    public function loginUser()
    {
        $login = $_POST['login'];
        $password = $_POST['password'];


        $hashedData = sundayLeagueDAO::hashedPassword($login);
        if (!$hashedData) {
            throw new Exception("Błędny login lub hasło");
        }
        if (!password_verify($password, $hashedData['password'])) {
            throw new Exception("Błędny login lub hasło");
        }
        $user = sundayLeagueDAO::loginUser($hashedData['id']);
        $user = sundayLeagueDAO::getUserRole($user);

        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+1 hour')->getTimestamp();      // Add 60 seconds
        $serverName = "sundayleague";

        $data = [
            'iat'  => $issuedAt->getTimestamp(),  // Czas wygenerowania tokena
            'iss'  => $serverName,                // Wnioskujący
            'nbf'  => $issuedAt->getTimestamp(),  // Min. czas prośby o nowy token
            'exp'  => $expire,                    // Czas wygasnieca tokena
            'user' => $user,                      // Dane nt. uzytkownika
        ];

        $jwt = JWT::encode(
            $data,
            PRIVATEKEY,
            ALGORITHM,
        );
        echo Json::toJson($jwt);
        return;
    }

    public function getAllUsers()
    {
        self::authenticate(sundayLeagueDAO::ADMIN);
        $users = sundayLeagueDAO::getUsers();
        $roles = sundayLeagueDAO::getRoles();

        echo Json::toJson(["users" => $users, "roles" => $roles]);
        return;
    }

    public function updateUser()
    {
        self::authenticate(sundayLeagueDAO::ADMIN);

        $user = $_POST['user'];
        sundayLeagueDAO::updateUser($user);
        echo Json::toJson("Success");
        return;
    }

    public function addComment()
    {
        self::authenticate([sundayLeagueDAO::ADMIN, sundayLeagueDAO::REDAKTOR, sundayLeagueDAO::UZYTKOWNIK]);

        $article = $_POST['article'];
        $comment = $_POST['comment'];
        $user =  $_POST['user'];

        $newComment = sundayLeagueDAO::addCommentToArticle($article, $comment, $user);
        echo Json::toJson($newComment);
        return;
    }

    public function updatePassword()
    {
        self::authenticate([sundayLeagueDAO::ADMIN, sundayLeagueDAO::REDAKTOR, sundayLeagueDAO::UZYTKOWNIK]);
        $newPassword = $_POST['newPassword'];
        $oldPassword = password_hash($_POST['oldPassword'], PASSWORD_BCRYPT);
        $id = $_POST['id'];

        $hashedData = sundayLeagueDAO::hashedPassword2($id);
        if (!password_verify($oldPassword, $hashedData['password'])) {
            throw new Exception("Błędny login lub hasło");
        }
        $return = sundayLeagueDAO::updateUserPassowrd($id, $newPassword);
        if (!$return) {
            throw new Exception("Coś nie tak podczas aktualizowania hasła");
        }
        echo Json::toJson("Success");
        return;
    }

    public function updateAvatar()
    {
        self::authenticate([sundayLeagueDAO::ADMIN, sundayLeagueDAO::REDAKTOR, sundayLeagueDAO::UZYTKOWNIK]);

        $newAvatar = $_POST['newAvatar'];
        $id  = $_POST['id'];
        sundayLeagueDAO::updateAvatar($id, $newAvatar);
        echo Json::toJson("Succes");
        return;
    }

    public function deleteComment()
    {
        self::authenticate([sundayLeagueDAO::ADMIN, sundayLeagueDAO::REDAKTOR]);
        $commentId = $_POST['commentId'];
        sundayLeagueDAO::deleteComment($commentId);
        echo Json::toJson("Succes");
        return;
    }

    public function deleteArticle()
    {
        self::authenticate([sundayLeagueDAO::ADMIN, sundayLeagueDAO::REDAKTOR]);
        $articleId = $_POST['article_id'];
        sundayLeagueDAO::deleteArticle($articleId);
        echo Json::toJson("Succes");
        return;
    }
    public function deleteUser()
    {
        self::authenticate(sundayLeagueDAO::ADMIN);
        $userId = $_POST['userId'];
        sundayLeagueDAO::deleteUser($userId);
        echo Json::toJson("Succes");
        return;
    }

    public function saveChangesArticle()
    {

        self::authenticate([$this->admin, $this->redaktor]);
        $article = $_POST['article'];
        sundayLeagueDAO::updateArticle($article);
        echo Json::toJson("Succes");
        return;
    }

    public function saveSchedule()
    {
        self::authenticate([$this->admin, $this->redaktor]);
        $schedule = $_POST['schedule'];
        sundayLeagueDAO::updateSchedule($schedule);
        echo Json::toJson("Succes");
        return;
    }
}
