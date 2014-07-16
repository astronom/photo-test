<?php
/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 16.07.14
 * Time: 22:15
 */

abstract class AController {

	public $request;

	public static function init(Array $_request)
	{
		$c = new Controller();
		$c->request = $_request;

		return $c;
	}

	/**
	 * Renders a view file.
	 * This method includes the view file as a PHP script
	 * and captures the display result if required.
	 * @param string $_viewFile_ view file
	 * @param array $_data_ data to be extracted and made available to the view file
	 * @param boolean $_return_ whether the rendering result should be returned as a string
	 * @return string the rendering result. Null if the rendering result is not required.
	 */
	public function render($_viewFile_,$_data_=null,$_return_=true)
	{
		// we use special variable names here to avoid conflict when extracting data
		if(is_array($_data_))
			extract($_data_,EXTR_PREFIX_SAME,'data');
		else
			$data=$_data_;
		if($_return_)
		{
			ob_start();
			ob_implicit_flush(false);
			require($_viewFile_);
			echo ob_get_clean();
		}
		else
			require($_viewFile_);
	}

	/**
	 * Return data to browser as JSON
	 *
	 * @param array $data
	 */
	protected function renderJSON($data)
	{
		header('Content-type: application/json');
		echo json_encode($data);

		exit();
	}
} 