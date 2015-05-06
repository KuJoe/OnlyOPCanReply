<?php
/*
	Only OP Can Reply by KuJoe <www.jmd.cc>
*/

// Don't allow direct initialization.
if (! defined('IN_MYBB')) {
	die('Nope.');
}
$plugins->add_hook("newreply_do_newreply_start", "onlyopcanreply");
$plugins->add_hook("newreply_do_newreply_end", "onlyopcanreply");
$plugins->add_hook("newreply_start", "onlyopcanreply");
$plugins->add_hook("newreply_end", "onlyopcanreply");

// The info for this plugin.
function opcr_info() {
	return array(
		'name'			=> 'Only OP Can Reply',
		'description'	=> 'Only the OP can reply to threads in a specific forum.',
		'website'		=> 'http://jmd.cc',
		'author'		=> 'KuJoe',
		'authorsite'	=> 'http://jmd.cc',
		'version'		=> '1.0',
		'compatibility'	=> '18*',
		'codename'		=> 'opcr',
	);
}

function opcr_activate() {
	global $db;

	$opcr_group = array(
		'name'			=> 'opcr',
		'title'			=> 'Only OP Can Reply',
		'description'	=> 'Only OP Can Reply Settings.',
		'disporder'		=> '99',
		'isdefault'		=> 'no'
	);

	$db->insert_query('settinggroups', $opcr_group);
	$gid = $db->insert_id();

	$opcr_setting_1 = array(
		'name'			=> 'opcr_fid',
		'title'			=> 'Forum ID(s)',
		'description'	=> 'Enter the forum ID(s) that you want only thread authors to be able to reply. (Comma seperated, 0 to disable.)',
		'optionscode'	=> 'text',
		'value'		=> '0',
		'disporder'		=> 1,
		'gid'			=> intval($gid)
	);
	$opcr_setting_2 = array(
		'name'			=> 'opcr_staff',
		'title'			=> 'Moderator Permissions',
		'description'	=> 'Do you want to allow those people with moderator permissions to reply?',
		'optionscode'	=> 'onoff',
		'value'		=> '1',
		'disporder'		=> 2,
		'gid'			=> intval($gid)
	);	
	
	$db->insert_query('settings', $opcr_setting_1);
	$db->insert_query('settings', $opcr_setting_2);
	
	rebuild_settings();
	
}

// Action to take to deactivate the plugin.
function opcr_deactivate() {
	global $mybb, $db, $cache, $templates;

	//Remove settings.
	$db->delete_query("settings","name='opcr_fid'");
	$db->delete_query("settings","name='opcr_staff'");
	$db->delete_query("settinggroups","name='opcr'");

	rebuild_settings();

}

function onlyopcanreply()
{
	global $mybb, $db, $message, $reply, $thread, $lang, $theme;

	$opcrs = $mybb->settings['opcr_staff'];
	$opcrf = explode(",",$mybb->settings['opcr_fid']);

	if($opcrf > 0 && $mybb->user['uid'] != $thread['uid'] && !is_moderator($fid) && in_array($thread['fid'],$opcrf)) {
		error("Only the thread creator is allowed to reply.");
	}
	if($opcrf > 0 && $opcrs == 0 && is_moderator($fid) && $mybb->user['uid'] != $thread['uid'] && in_array($thread['fid'],$opcrf)) {
		error("Only the thread creator is allowed to reply.");
	}
}
?>
