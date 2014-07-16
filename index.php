<?php
/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 08.07.14
 * Time: 13:41
 */
require "vendor/autoload.php";
require 'db.conf.php';

use DebugBar\StandardDebugBar;

App::init();

$router = new Hoa\Router\Http();
$router
		->get('index', '/', function (Array $_request) {
			Controller::init($_request)->index();
		})
		->get('cross tag add', '/crossTag/add/(?<tagId>\d+)/', function (Array $_request, $tagId) {
			Controller::init(array_merge($_request, array('tagId' => $tagId)))->addCrossTag();
		})
		->get('cross tag remove', '/crossTag/remove/(?<tagId>\d+)/', function (Array $_request, $tagId) {
			Controller::init(array_merge($_request, array('tagId' => $tagId)))->removeCrossTag();
		})
		->get('missed tag add', '/missedTag/add/(?<tagId>\d+)/', function (Array $_request, $tagId) {
			Controller::init(array_merge($_request, array('tagId' => $tagId)))->addMissedTag();
		})
		->get('missed tag remove', '/missedTag/remove/(?<tagId>\d+)/', function (Array $_request, $tagId) {
			Controller::init(array_merge($_request, array('tagId' => $tagId)))->removeMissedTag();
		})
		->get('like photo', '/like/(?<photoId>\d+)/', function (Array $_request, $photoId) {
			Controller::init(array_merge($_request, array('photoId' => $photoId)))->like();
		});

$dispatcher = new Hoa\Dispatcher\Basic();
$dispatcher->dispatch($router);


//$debug = new StandardDebugBar();
//$debugRenderer = $debug->getJavascriptRenderer();

