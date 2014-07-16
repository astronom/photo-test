<?php

/**
 * Created by PhpStorm.
 * User: astronom
 * Date: 16.07.14
 * Time: 21:16
 */
class Controller extends AController
{

	public function index()
	{
		list($userTags, $userTagsList) = $this->getUserTags();

		if (!empty($userTags))
			$stmt = App::$db->select('photos.id, photos.url, photos.date')
					->from('photos photos')
					->join('INNER JOIN photo_tags ON photo_tags.photo_id = photos.id
													  AND photo_tags.tag_id IN ( ' . implode(",", $userTags) . ' )')
					->groupBy('photos.id')
					->having('count( photos.id ) = ' . count($userTags))
					->orderBy('photos.date DESC')
					->limit(20, $this->getCurrentPage() * 20)
					->execute();
		else
			$stmt = App::$db->select('photos.id, photos.url, photos.date')
					->from('photos photos')
					->orderBy('photos.date DESC')
					->limit(20, $this->getCurrentPage() * 20)
					->execute();

		$photoList = array();

		while ($photo = $stmt->fetchInto(new Photo(), 'photos'))
			$photoList[] = $photo;

		$this->render('view/index.php', array(
				'photoList' => $photoList,
				'userTags'  => $userTags,
				'userTagsList' => $userTagsList,
				'paging' => $this->getPagination()
		));
	}

	public function addCrossTag()
	{
		if (!empty($this->request['tagId']))
			$this->addTag((int)$this->request['tagId'], 'cross');

		$this->index();
	}

	public function removeCrossTag()
	{
		if (!empty($this->request['tagId']))
			$this->removeTag((int)$this->request['tagId'], 'cross');

		$this->index();
	}


	public function like()
	{
		if (!empty($this->request['user_id']))
			$user_id = (int)$this->request['user_id'];
		else
			$user_id = false;

		if (!empty($this->request['photo_id']))
			$photo_id = (int)$this->request['photo_id'];
		else
			$photo_id = false;

		if (!$user_id || !$photo_id) {
			$this->renderJSON(array('success' => false, 'error' => 'empty user or photo'));
		}

		try {
			$result = App::$db->insert('likes', array(
					'photo_id' => $photo_id,
					'user_id'  => $user_id,
			));

			if ($result)
				$this->renderJSON(array('success' => true));
			else
				$this->renderJSON(array('success' => false));

		} catch (PDOException $e) {
			$this->renderJSON(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	public function getCurrentPage()
	{
		if (!empty($this->request['page']))
			return (int)$this->request['page'];
		else
			return 0;
	}

	public function getUserTags()
	{
		$userTags = is_array(App::$session['cross']) ? App::$session['cross'] : array();
		$userTagsList = array();

		if (!empty($userTags)) {
			$stmt = App::$db->select('t.id, t.name')
					->from('tags t')
					->whereIn('id', $userTags)
					->execute();
			while ($tag = $stmt->fetchInto(new Tag(), 't'))
				$userTagsList[] = $tag;

		}

		return array($userTags, $userTagsList);
	}

	public function getPagination()
	{
		$userTags = is_array(App::$session['cross']) ? App::$session['cross'] : array();

		if(!empty($userTags))
		{
			$totalPhotos = App::$db->execQueryString('
				SELECT COUNT(*) as counter FROM (
					SELECT photos.id, photos.url, photos.date
						FROM photos photos
					INNER JOIN photo_tags ON photo_tags.photo_id = photos.id
										  AND photo_tags.tag_id IN ( '.implode(",", $userTags).' )
					GROUP BY photos.id
					HAVING count( photos.id ) = '.count($userTags).'
					) as countAll')->fetchColumn(0);
		}
		else
			$totalPhotos = App::$db->count('photos', '');

		$paging = new Pagination();
		$paging->set('urlscheme','/?page=%page%');
		$paging->set('perpage',20);
		$paging->set('page',max(1,intval($this->getCurrentPage())));
		$paging->set('total',$totalPhotos);
		$paging->set('nexttext','Next Page');
		$paging->set('prevtext','Previous Page');
		$paging->set('focusedclass','selected');
		$paging->set('delimiter','   ');
		$paging->set('numlinks',9);

		return $paging;

	}

	protected function addTag($tagId, $name)
	{
		if (App::$session->isEmpty()) {
			App::$session[$name] = array();
		} else {
			$userTags = App::$session[$name];
			$userTags[] = $tagId;
			$userTags = array_unique($userTags);

			App::$session[$name] = $userTags;
		}

		return App::$session[$name];
	}

	protected function removeTag($tagId, $name)
	{
		if (App::$session->isEmpty()) {
			App::$session[$name] = array();
		} else {
			$userTags = array_filter(App::$session[$name], function ($var) use ($tagId) {
				return $var == $tagId ? false : true;
			});

			App::$session[$name] = $userTags;
		}

		return App::$session[$name];
	}
} 