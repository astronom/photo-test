<?php
/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 08.07.14
 * Time: 18:26
 */

if(!empty($_GET['user_id']))
	$user_id = (int) $_GET['user_id'];

if(!empty($_GET['photo_id']))
	$photo_id = (int) $_GET['photo_id'];

$db = require_once realpath(__DIR__.DIRECTORY_SEPARATOR.'db.conf.php');

header('Content-type: application/json');

if(mysqli_query($db, "INSERT INTO likes (`photo_id`, `user_id`) VALUES ($photo_id, $user_id)"))
	echo json_encode(array('success' => true));
else
	echo json_encode(array('success' => false));