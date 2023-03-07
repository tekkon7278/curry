<?php

class AccessInfo
{
	public static function isAdmin($accessHost)
	{
		$ini = Ini::load('system.ini', 'admin_hosts');
		if (isset($ini['hosts']) && is_array($ini['hosts'])) {
			foreach ($ini['hosts'] as $host) {
				if ($accessHost == $host) {
					return true;
				}
			}
		}
		return false;
	}
	
	public static function isRobot($agent)
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

}