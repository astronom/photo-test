<?php
/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 16.07.14
 * Time: 21:54
 */

class Photo extends AModel {

	/**
	 * @var
	 */
	public $tags;

	/**
	 * @return mixed Tag[]|array
	 * @throws Exception
	 */
	public function getTags()
	{
		$photoTagsStmt = $this->getDbConnection()->select('t.id, t.name')
							->from('tags t')
							->join('LEFT JOIN photo_tags photo_tags ON ( photo_tags.tag_id = t.id )')
							->where('photo_tags.photo_id = ?', $this->id)
							->execute();

		$this->tags = array();

		while($photoTag = $photoTagsStmt->fetchInto(new Tag(), 't'))
			$this->tags[] = $photoTag;

		return $this->tags;
	}
} 