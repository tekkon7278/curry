<?php

class Counter extends Model
{
	public function getCount($date = null, $field = null)
	{
		$sel = $this->select();
		if (null != $date) {
			$sel->where('date', $date);
		}
		if ($field == null) {
			$field = 'unique_user';
		}
		$cnt = $sel->fetchSum($field);
		return $cnt;
	}

	public function getCounterInfo($field = null)
	{
		$today = date('Ymd');
		$yesterday = date('Ymd', strtotime('-1 day'));
		$counter["total"] = $this->getCount(null, $field);
		$counter["today"] = $this->getCount($today, $field);
		$counter["yesterday"] = $this->getCount($yesterday, $field);
		
		return $counter;
	}
	
	public function incrementCount($host, $agent, $visited)
	{
		$db = Db::factory();
		
		try
		{
			$date = date('Ymd');
			
			$db->beginTransaction();
			
			// ロボットか
			$isRobot = $this->isRobot($agent);
			
			// 日付が変わっていないかチェック
			$isNewDate = $this->checkDateChange($date);
			if ($isNewDate) {
				// 日付が変わっている場合は挿入
				$ins = $this->insert();
				$ins->values('date', $date);
				if ($isRobot) {
					$ins->values('robot_access', 1);
				} else {
					$ins->values('page_view', 1);
					$ins->values('unique_user', 1);
				}
				$ins->execute();
			} else {
				// 日付が変わっていない場合は更新
				$upd = $this->update()->where('date', $date);
				if ($isRobot) {
					$fields = array('robot_access');
				} else {
					$fields = array('page_view');
					if (!$visited) {
						// その日の初訪問ならUniqueUserもインクリメント
						$fields[] = 'unique_user';
					}
				}
				$upd->increment($fields);
			}
			
			$db->commit();
			return true;
			
		} catch (Exception $e) {
			$db->rollback();
			throw $e;
		}
	}
	
	public function incrementField($field, $agent)
	{
		$db = Db::factory();
		
		try
		{
			$date = date('Ymd');
			
			$db->beginTransaction();
			
			// ロボットか
			$isRobot = $this->isRobot($agent);

			// 日付が変わっていないかチェック
			$isNewDate = $this->checkDateChange($date);
			if ($isNewDate) {
				// 日付が変わっている場合は挿入
				$ins = $this->insert();
				$ins->values('date', $date);
				if ($isRobot) {
					$ins->values('robot_access', 1);
				} else {
					$ins->values($field, 1);
				}
				$ins->execute();
			} else {
				// 日付が変わっていない場合は更新
				$upd = $this->update()->where('date', $date);
				if ($isRobot) {
					$fields = array('robot_access');
				} else {
					$fields = array($field);
				}
				$upd->increment($fields);
			}
			
			
			$db->commit();
			return true;
			
		} catch (Exception $e) {
			$db->rollback();
			throw $e;
		}
	}
	
	protected function isRobot($agent)
	{
		$robots = array(
			'ICC-Crawler',
			'Teoma',
			'Y!J-BSC',
			'Pluggd\/Nutch',
			'psbot',
			'CazoodleBot',
			'Googlebot',
			'Antenna',
			'BlogPeople',
			'AppleWebKitOpenbot',
			'NaverBot',
			'PlantyNet',
			'livedoor',
			'msnbot',
			'FlashGet',
			'WebBooster',
			'MIDown',
			'moget',
			'InternetLinkAgent',
			'Wget',
			'InterGet',
			'WebFetch',
			'WebCrawler',
			'ArchitextSpider',
			'Scooter',
			'WebAuto',
			'InfoNaviRobot',
			'httpdown',
			'Inetdown',
			'Slurp',
			'Spider',
			'^Iron33',
			'^fetch',
			'^PageDown',
			'^BMChecker',
			'^Jerky',
			'^Nutscrape',
			'Baiduspider',
			'TMCrawler'
		);
		if (preg_match("/(" . implode('|', $robots) . ")/m", $agent)) {
			return true;
		}
		if (!preg_match("/Win/", $agent) && !preg_match("/Mac/", $agent) && !preg_match("/Linux/", $agent)) {
			return true;
		}
		return false;
	}

	protected function checkDateChange($date)
	{
		$sel = $this->select();
		$sel->where('date', $date);
		$cnt = $sel->fetchCount();
		if (false === $cnt || 0 < $cnt) {
			return false;
		}
		return true;
	}
	
	public function increment($request)
	{
		$cookie = new Cookie();
		$visited = $cookie->get('visited');
		if (!$visited) {
			$stamp = strtotime(date('Ymd') . '235959');
			$cookie->setExpire($stamp);
			$cookie->set('visited', true);
		}
		$host = gethostbyaddr($request->getServer('REMOTE_ADDR'));
		$agent = $request->getServer('HTTP_USER_AGENT');
		$this->incrementCount($host, $agent, $visited);
	}
}