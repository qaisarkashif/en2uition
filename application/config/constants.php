<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Development Constants
|--------------------------------------------------------------------------
|
 */
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('DS', DIRECTORY_SEPARATOR);

define('PROSLD_LIMIT', 25); // Profiles slider on the Homepage
define('COMMENTS_LIMIT', 25); // Comments under photo and profile
define('UPDATES_LIMIT', 15); // Updates on Homepage
define('NEIGHB_INDEX_LIMIT', 25); // "Neighborhoods by color" page (main)
define('NEIGHB_USERNAME_LIMIT', 50); //"Neighborhoods by username" page
define('NEIGHB_LASTPOST_LIMIT', 40); //"Neighborhoods by last post date" page

define('ORIG_PHOTO_PATH', '/uploads/profiles/pro-%s/albums/alb-%s/%s');
define('MEDIUM_PHOTO_PATH', '/uploads/profiles/pro-%s/albums/alb-%s/medium/%s');
define('THUMB_PHOTO_PATH', '/uploads/profiles/pro-%s/albums/alb-%s/thumbnail/%s');

define('USER_AVA_FOLDER', '/uploads/profiles/pro-%s/avatars');
define('USER_AVA_FORUM', '/uploads/profiles/pro-%s/avatars/forum_thumb.%s');
define('USER_AVA_HOMEPAGE', '/uploads/profiles/pro-%s/avatars/homepage_thumb.%s');
define('USER_AVA_PROFILE', '/uploads/profiles/pro-%s/avatars/profile_thumb.%s');
define('USER_AVA_FRIEND', '/uploads/profiles/pro-%s/avatars/friend_thumb.%s');
define('USER_AVA_ORIG', '/uploads/profiles/pro-%s/avatars/%s');

define('DEF_USER_AVA_FORUM', '/assets/img/pro-0/forum.png');
define('DEF_USER_AVA_HOMEPAGE', '/assets/img/pro-0/homepage.png');
define('DEF_USER_AVA_PROFILE', '/assets/img/pro-0/profile.png');
define('DEF_USER_AVA_FRIEND', '/assets/img/pro-0/friend.png');
define('DEF_USER_AVA_ORIG', '/assets/img/pro-0/original.png');

/* End of file constants.php */
/* Location: ./application/config/constants.php */