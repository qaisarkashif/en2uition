<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "home";

$route['signup'] = "/auth/signup";
$route['signin'] = "/auth/signin";
$route['signout'] = "/auth/signout";

$route['visitor/uid-(:num)'] = "/profile/visitor/$1";
$route['visitor/photo/uid-(:num)'] = "/photo/page/$1";

$route['unfriend'] = '/profile/unfriend';

$route['activate/(:num)/(:any)'] = "/auth/activate/$1/$2";
$route['language/(:any)'] = "/language/set_language/$1";

$route['get_shared_answers'] = "/answer/get_shared_answers";
$route['questionnaire/answers/shared'] = "/questionnaire/view_shared_answers/past";
$route['questionnaire/answers/shared/past'] = "/questionnaire/view_shared_answers/past";
$route['questionnaire/answers/shared/present'] = "/questionnaire/view_shared_answers/present";
$route['questionnaire/progress/get'] = "/questionnaire/get_level_progress";
$route['questionnaire/privacy_question/list'] = "/questionnaire/get_privacy_question";
$route['questionnaire/(:any)/level(:num)/analyze'] = "/question/analyze/$1/$2";
$route['outcome/analyze'] = "/question/analyze/past/7";

$route['questions/(:any)/l(:num)/q(:num)'] = "/question/show/$1/$2/$3";
$route['questions/privacy/update'] = "/question/update_privacy_code";
$route['questions/answer/save'] = "/answer/save";
$route['questions/answered'] = "/answer/view_answered";
$route['questions/mode/(:any)'] = "/question/change_mode/$1";

$route['photo/album-(:num)/edit'] = "/photo/album/$1";
$route['photo/album-(:num)/delete'] = "/photo/delete_album/$1";
$route['photo/privacy/edit'] = "/photo/edit_privacy";
$route['photo/privacy/update'] = "/photo/update_photo/privacy";
$route['photo/title/update'] = "/photo/update_photo/title";
$route['photo/delete'] = "/photo/delete_photo";
$route['photo/share'] = "/photo/share_photo";
$route['photo/page/preset/(:num)/(:num)/(:num)'] = "/photo/preset_page/$1/$2/$3";
$route['photo/page/preset/(:num)/(:num)'] = "/photo/preset_page/$1/$2";

$route['vote/add/(:any)'] = "/vote/add_vote/$1";

$route['request/check'] = "/request/check_requests";
$route['request/add'] = "/request/add_request";
$route['request/response'] = "/request/response_to_request";

$route['messages'] = "/message/index";
$route['message/mark_unread'] = "/message/mark_as_unread";
$route['messages/unread/number'] = "/message/get_unread_number";
$route['messages/history/msg-(:num)'] = "/message/view_history/$1";

$route['users/block'] = "/profile/update_blacklist/add";
$route['users/unblock'] = "/profile/update_blacklist/remove";

$route['get_usernames'] = "/neighborhood/get_usernames";
$route['neighborhood/username_ajax'] = "/neighborhood/by_username_ajax";
$route['neighborhood/username/(:any)'] = "/neighborhood/by_username/$1";
$route['neighborhood/date/(:any)/(:any)/(:any)/my_topics'] = "/neighborhood/by_date/$1/$2/$3/true";
$route['neighborhood/date/(:any)/(:any)/(:any)'] = "/neighborhood/by_date/$1/$2/$3";
$route['neighborhood/(:any)'] = "/neighborhood/index/$1";
$route['neighborhood/(:any)/(:any)'] = "/neighborhood/index/$1/$2";
$route['new_topic/(:any)/(:any)'] = "/neighborhood/new_topic/$1/$2";
$route['add-new-topic'] = "/neighborhood/add_new_topic";
$route['forum/topic/add-comment'] = "/neighborhood/comment_topic";
$route['forum/topic/delete-comment'] = "/neighborhood/delete_comment";
$route['forum/topic/show-comments'] = "/neighborhood/load_replies";
$route['forum/topic-(:num)/(:any)/(:any)'] = "/neighborhood/topic/$1/$2/$3";

$route['notifications/get'] = "/ajax/get_notifications";

$route['compare'] = "/homepage/compare";
$route['compare/(:any)'] = "/homepage/compare/$1";
$route['compare/(:any)/(:any)'] = "/homepage/compare/$1/$2";
$route['group-description/(:any)/(:any)'] = "/homepage/group_description/$1/$2";
$route['group-description'] = "/homepage/group_description";
$route['my-group/(:any)/(:any)'] = "/homepage/group_description/$1/$2";
$route['predictions'] = "/homepage/predictions";
$route['my-prediction'] = "/homepage/predictions";
$route['crop-profile-image'] = "/ajax/crop_profile_image";
$route['remove-avatar'] = "/profile/remove_avatar";
$route['send-feedback'] = "/home/send_feedback";

$route['relationship/in_relationship'] = "/relationship/index/in_relationship";
$route['relationship/single'] = "/relationship/index/single";
$route['set_usercolor'] = "/relationship/set_user_color";
$route['join'] = "/relationship/join";

$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */