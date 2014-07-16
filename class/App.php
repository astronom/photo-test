<?php

/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 16.07.14
 * Time: 22:02
 */

use Hoa\Session\Session as Session;

event('hoa://Event/Session/user:expired')
		->attach(function (Hoa\Core\Event\Bucket $bucket) {

});

/**
 * Class App
 */
class App
{
	/**
	 * @var database\DB
	 */
	public static $db;

	/**
	 * @var Hoa\Session\Session
	 */
	public static $session;

	public static function init()
	{
		try {
			self::$db = new database\DB(DB_DSN, DB_USER, DB_PASS);
			self::$db->setFetchTableNames(1);
		} catch (PDOException $e) {
			echo $e->getMessage();
			die();
		}

		self::$session = new Session('user');
	}
} 