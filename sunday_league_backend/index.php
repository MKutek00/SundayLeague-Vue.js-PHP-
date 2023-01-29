<?php
require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);


Router::post('addArticle', 'sundayLeague');
Router::post('deleteArticle', 'sundayLeague');
Router::get('getArticles', 'sundayLeague');
Router::post('getArticle', 'sundayLeague');
Router::post('saveChangesArticle', 'sundayLeague');
Router::post('addComment', 'sundayLeague');
Router::post('deleteComment', 'sundayLeague');

Router::post('saveSchedule', 'sundayLeague');
Router::get('getLeagues', 'sundayLeague');
Router::post('getLeague', 'sundayLeague');
Router::post('getTeamTable', 'sundayLeague');

Router::post('getLeagueSchedule', 'sundayLeague');
Router::post('getCloseGames', 'sundayLeague');

Router::get('getAllUsers', 'sundayLeague');
Router::post('updateUser', 'sundayLeague');
Router::post('deleteUser', 'sundayLeague');

Router::post('registerUser', 'sundayLeague');
Router::post('loginUser', 'sundayLeague');
Router::post('updateAvatar', 'sundayLeague');
Router::post('updatePassword', 'sundayLeague');

Router::get('scrapTeamsAndLeagues', 'sundayLeague');
Router::get('scrapSchedule', 'sundayLeague');


Router::get('test', 'sundayLeague');
Router::run($path);
