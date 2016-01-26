<?php

namespace TMCms\Modules\Sessions;

use TMCms\Modules\IModule;
use TMCms\Traits\singletonInstanceTrait;
use TMCms\Modules\Sessions\Entity\SessionEntity;
use TMCms\Modules\Sessions\Entity\SessionEntityRepository;

defined('INC') or exit;

class ModuleSessions implements IModule
{
	use singletonInstanceTrait;

	public static $tables = array(
		'sessions' => 'm_sessions'
	);

	private static $ttl_in_seconds = 3600; // One hour
	private static $cookie_name = 'sid';

	private static $_check_cache;
	private static $_sid = '';
	private static $_session_data = '';
	private static $_hash_uid = ''; // Required to check hash with sid

	public static function start($user_id, $data = [])
	{
		// No user id supplied
		if (!$user_id || !ctype_digit((string)$user_id)) {
			return false;
		}

		// Remove old DB entries
		self::removeOld();

		// Generate unique session id
		while (($sid = self::generateSidHash()) && q_check(self::$tables['sessions'], '`sid` = "' . $sid . '"')) ;

		// Prepare data for db
		$data = serialize($data);

		// Save to local cache, prevents recalculations
		self::$_sid = $sid;
		self::$_hash_uid = $sid;
		self::$_session_data = $data;

		// Create db entry for current session
		$session = new SessionEntity();
		$session->setSid($sid);
		$session->setUserId($user_id);
		$session->setData($data);
		$session->save();

		// Set cookie in browser with main info
		setcookie(self::$cookie_name, $sid, 0, '/');

		// Create session data to check in future requests
		$_SESSION['user_id'] = $user_id;
		$_SESSION[self::$cookie_name] = $sid; // Current session id
		$_SESSION['uid'] = self::$_hash_uid; // Unique id for hash generation, required to check hash

		return $session;
	}

	private static function generateSidHash($uid = '')
	{
		// Generate random if not supplied
		if (!$uid) {
			$uid = uniqid(mt_rand(), 1);
		}

		// Save for other functions
		self::$_hash_uid = $uid;

		return md5(VISITOR_HASH . $uid . VISITOR_HASH);
	}

	public static function stop()
	{
		// Not exists in browser
		if (!isset($_COOKIE[self::$cookie_name])) {
			return false;
		}

		// Value in browser is not correct
		$sid = $_COOKIE[self::$cookie_name];
		if (!strlen($sid) === 32 || !ctype_alnum($sid)) {
			return false;
		}

		// Clear session, fill with empty data
		setcookie(self::$cookie_name, '', 86400, '/');

		// Delete session from db
		$sessions = new SessionEntityRepository();
		$sessions->setWhereSid($sid);
		$sessions->deleteObjectCollection();

		// Remove from server session
		unset($_SESSION['user_id'], $_SESSION[self::$cookie_name], $_SESSION['uid']);

		// Remove from local cache
		self::$_sid = '';
		self::$_hash_uid = '';
		self::$_session_data = '';

		return true;
	}

	/**
	 * Get current existing sid name
	 * @return string
	 *
	 */
	public static function getSid()
	{
		$sid = NULL;

		if (isset(self::$_sid) && self::$_sid) { // Check in local cache
			$sid = self::$_sid;
		} elseif (isset($_SESSION[self::$cookie_name]) && $_SESSION[self::$cookie_name]) { // Check server session
			$sid = $_SESSION[self::$cookie_name];
		} elseif (isset($_COOKIE[self::$cookie_name]) && $_COOKIE[self::$cookie_name]) { // Check cookie
			$sid = $_COOKIE[self::$cookie_name];
		}

		// Check length
		if (strlen($sid) !== 32 || !ctype_alnum($sid)) {
			$sid = NULL;
		}

		return $sid;
	}

	/**
	 * Get data saved in current session
	 * @return array
	 */
	public static function getData()
	{
		$sid = self::getSid();
		if (!$sid) {
			return NULL;
		}

		// Saved in local cache
		if (self::$_session_data) {
			return unserialize(self::$_session_data);
		}

		// Or get from db
		$sessions = new SessionEntityRepository();
		$sessions->setWhereSid($sid);
		/** @var SessionEntity $session */
		$session = $sessions->getFirstObjectFromCollection();
		if (!$session) {
			return NULL;
		}

		return unserialize($session->getData());
	}

	public static function check($touch = false, $return_data = false)
	{
		// Maybe we need just to check
		if (!$touch && !$return_data && self::$_check_cache) {
			return self::$_check_cache;
		}

		// Current sid
		$sid = self::getSid();
		if (!$sid) {
			return false;
		}

		// Find session entry in db
		$sessions = new SessionEntityRepository();
		$sessions->setWhereSid($sid);
		/** @var SessionEntity $session */
		$session = $sessions->getFirstObjectFromCollection();
		if (!$session) {
			return false;
		}

		// Maybe it is old session
		if (NOW - $session->getTs() > self::$ttl_in_seconds) {
			// Remove all session data
			self::stop();

			// Save locally
			self::$_check_cache = NULL;

			return false;
		}

		// Save to local cache that session exists
		self::$_check_cache = $session;

		// Update session
		if ($touch) {
			self::touch(self::$_sid);
		}

		// Need to return stored data
		if ($return_data) {
			return self::getData();
		}

		return $session;
	}

	/**
	 * Update current session timestamp
	 * @param string $sid
	 */
	public static function touch($sid = NULL)
	{
		if (!$sid) {
			$sid = self::getSid();
		}

		if (!$sid) {
			return;
		}
		$sessions = new SessionEntityRepository();
		$sessions->setWhereSid($sid);
		$sessions->setTs(NOW);
		$sessions->save();
	}

	/**
	 * @param int $ttl hals a day by default
	 * @return bool
	 */
	private static function removeOld($ttl = 43200)
	{
		// Chance of 0.1% to clean up
		if (mt_rand(0, 999)) {
			return false;
		}

		$sessions = new SessionEntityRepository();
		$sessions->addWhereFieldIsLower('ts', (NOW - $ttl));
		$sessions->deleteObjectCollection();

		return true;
	}
}