<?php
/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 16.07.14
 * Time: 22:01
 */

abstract class AModel {

	/**
	 * @var database\DB
	 */
	public static $db;

	public function getDbConnection()
	{
		if(self::$db!==null)
			return self::$db;
		else
		{
			self::$db = App::$db;
			if(self::$db instanceof database\DB)
				return self::$db;
			else
				throw new Exception('database\DB not initialized');
		}
	}
} 