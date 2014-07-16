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
		->get('i', '/', function (Array $_request) {
			Controller::init($_request)->index();
		})
		->get('ca', '/crossTag/add/(?<tagId>\d+)/', function (Array $_request, $tagId) {
			Controller::init(array_merge($_request, array('tagId' => $tagId)))->addCrossTag();
		})
		->get('cr', '/crossTag/remove/(?<tagId>\d+)/', function (Array $_request, $tagId) {
			Controller::init(array_merge($_request, array('tagId' => $tagId)))->removeCrossTag();
		});

$dispatcher = new Hoa\Dispatcher\Basic();
$dispatcher->dispatch($router);


//$debug = new StandardDebugBar();
//$debugRenderer = $debug->getJavascriptRenderer();

