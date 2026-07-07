<?
require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/controller.php');

$router = new Router();
$router->route($_POST);
