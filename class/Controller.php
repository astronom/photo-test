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
		list($userCrossTags, $userCrossTagsList) = $this->getUserTags('cross');
		list($userMissedTags, $userMissedTagsList) = $this->getUserTags('missed');

		$stmt = App::$db->select('photos.id, photos.url, photos.date')
				->from('photos photos')
				->limit(20, $this->getCurrentPage() * 20)
				->orderBy('photos.date DESC');

		$photosIdsWhithMissedTags = array();

		if (!empty($userCrossTags) || !empty($userMissedTags)) {

			$joinStr = 'INNER JOIN photo_tags ON photo_tags.photo_id = photos.id';

			if (!empty($userCrossTags)) {
				$joinStr .= ' AND photo_tags.tag_id IN ( ' . implode(",", $userCrossTags) . ' )';
				$stmt->having('count( photos.id ) = ' . count($userCrossTags));
			}

			if (!empty($userMissedTags)) {

				$stmtPhotosWhithMissedTags = App::$db->select('photo_id')->from('photo_tags')->whereIn('tag_id', $userMissedTags)->groupBy('photo_id')->execute();
				$photosIdsWhithMissedTags = array();

				while ($missedPhotoId = $stmtPhotosWhithMissedTags->fetch(PDO::FETCH_COLUMN))
					$photosIdsWhithMissedTags[] = $missedPhotoId;

				$stmt->whereNotIn('photos.id', $photosIdsWhithMissedTags);
			}

			$stmt->join($joinStr);
			$stmt->groupBy('photos.id');
		}

		$photoList = array();

		try {
			$stmt = $stmt->execute();

			while ($photo = $stmt->fetchInto(new Photo(), 'photos'))
				$photoList[] = $photo;

		} catch (PDOException $e) {
			echo $e->getMessage();
		}

		$this->render('view/index.php', array(
				'photoList'          => $photoList,
				'userCrossTags'      => $userCrossTags,
				'userCrossTagsList'  => $userCrossTagsList,
				'userMissedTags'     => $userMissedTags,
				'userMissedTagsList' => $userMissedTagsList,
				'paging'             => $this->getPagination($photosIdsWhithMissedTags)
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

	public function addMissedTag()
	{
		if (!empty($this->request['tagId']))
			$this->addTag((int)$this->request['tagId'], 'missed');

		$this->index();
	}

	public function removeMissedTag()
	{
		if (!empty($this->request['tagId']))
			$this->removeTag((int)$this->request['tagId'], 'missed');

		$this->index();
	}


	public function like()
	{
//		if (!empty($this->request['user_id']))
//			$user_id = (int)$this->request['user_id'];
//		else
		$user_id = 1;

		if (!empty($this->request['photoId']))
			$photo_id = (int)$this->request['photoId'];
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

	public function getUserTags($name)
	{
		$userTags = is_array(App::$session[$name]) ? App::$session[$name] : array();
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

	public function getPagination($removePhotoIds = array())
	{
		$userCrossTags = is_array(App::$session['cross']) ? App::$session['cross'] : array();
		$userMissedTags = is_array(App::$session['missed']) ? App::$session['missed'] : array();

		if (!empty($userTags) || !empty($userMissedTags)) {
			$query = 'SELECT photos.id
						FROM photos photos
							INNER JOIN photo_tags ON photo_tags.photo_id = photos.id';

			if (!empty($userCrossTags))
				$query .= ' AND photo_tags.tag_id IN ( ' . implode(",", $userCrossTags) . ' )';


			if (!empty($removePhotoIds))
				$query .= ' WHERE photos.id NOT IN ( ' . implode(",", $removePhotoIds) . ' )';

			$query .= ' GROUP BY photos.id';

			if (!empty($userCrossTags))
				$query .= ' HAVING count( photos.id ) = ' . count($userCrossTags);

			$totalPhotos = 0;

			try {
				$totalPhotos = App::$db->execQueryString('SELECT COUNT(*) as counter FROM ( ' . $query . ' ) as countAll')->fetchColumn(0);
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		} else
			$totalPhotos = App::$db->count('photos', '');

		$paging = new Pagination();
		$paging->set('urlscheme', '/?page=%page%');
		$paging->set('perpage', 20);
		$paging->set('page', max(1, intval($this->getCurrentPage())));
		$paging->set('total', $totalPhotos);
		$paging->set('nexttext', 'Next Page');
		$paging->set('prevtext', 'Previous Page');
		$paging->set('focusedclass', 'selected');
		$paging->set('delimiter', '   ');
		$paging->set('numlinks', 9);

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