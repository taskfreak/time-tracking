<?php
/**
 * TaskFreak!
 *
 * @package taskfreak
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.4
 * @copyright GNU General Public License (GPL) version 3
 */

$GLOBALS['lang']['task'] = array(
	'priority'	=> 'priority',
	'spent'		=> 'spent',
	'note'		=> 'note',
	'todo'		=> 'todo',
	'done'		=> 'done',
	'valid'		=> 'valid',
	'archived'	=> 'archived',
	'running'	=> 'running',
	'paused'	=> 'paused'
);

$GLOBALS['lang']['priority'] = array(
	'urgent'		=> 'urgent',
	'important'		=> 'important',
	'high'			=> 'high',
	'quickly'		=> 'quickly',
	'pretty_soon'	=> 'pretty soon',
	'soon'			=> 'soon',
	'normal'		=> 'normal',
	'after'			=> 'after',
	'later'			=> 'later',
	'low'			=> 'low',
	'anytime'		=> 'anytime',
	'whenever'		=> 'whenever'
);

$GLOBALS['lang']['ui'] = array(
	'reload'		=> 'reload',
	'reload_list'	=> 'reload list',
	'task'			=> 'task',
	'tasks'			=> 'tasks',
	'create_single'	=> 'create single task',
	'create_multi'	=> 'create multiple task',
	'edit_task'		=> 'edit task',
	'delete_task'	=> 'delete task',
	'start_task'	=> 'start task',
	'mark_archived'	=> 'mark task as archived',
	'done_confirm'	=> 'really mark this task as completed ?',
	'compact'		=> 'compact',
	'expand'		=> 'expand',
	'select_all'	=> 'select all',
	'info'			=> 'info',
	'history'		=> 'history',
	'history_empty'	=> 'no time reports yet',
	'report_spent'	=> 'report time spent',
	'total'			=> 'total',
	'user_menu'		=> 'show user menu',
	'admin'			=> 'admin',
	'settings'		=> 'settings',
	'all_users'		=> 'all users',
	'switch'		=> 'switch',
	'switch_user'	=> 'see other user\'s tasks',
	'select_user'	=> 'select user whose tasks you want to see',
	'switched'		=> 'switched to another user',
	'task_manager'	=> 'task manager',
	'task_managers'	=> 'task managers',
	'user_admin'	=> 'user admin',
	'user_admins'	=> 'user admins',
	'last_visit'	=> 'last visit',
	'preferences'	=> 'my preferences',
	'create_user'	=> 'create user',
	'edit_user'		=> 'edit user',
	'delete_user'	=> 'delete user'
);

$GLOBALS['lang']['pages'] = array(
	'todo'			=> 'todo',
	'done'			=> 'done',
	'valid'			=> 'valid',
	'archives'		=> 'archives',
	'edit_task'		=> 'edit task',
	'create_task'	=> 'create task',
	'create_tasks'	=> 'create multiple tasks',
	'view_task'		=> 'task details'
);

$arrMess = array(
	'task_created'		=> 'task created',
	'task_updated'		=> 'task updated',
	'%_task_created'	=> '% task(s) created',
	'task_deleted'		=> 'task deleted',
	'time_added'		=> 'work time reported'
);
// we're adding these translations to the standard messages translations
$GLOBALS['lang']['message'] = $GLOBALS['lang']['message']+$arrMess;

$arrButton = array(
	'pause'				=> 'pause',
	'resume'			=> 'resume',
	'done'				=> 'done',
	'reopen'			=> 're-open',
	'postpone_1_day'	=> 'postpone 1 day',
	'postpone_2_days'	=> 'postpone 2 days',
	'postpone_1_week'	=> 'postpone 1 week',
	'postpone_2_weeks'	=> 'postpone 2 weeks',
	'postpone_1_month'	=> 'postpone 1 month',
	'postpone_2_months'	=> 'postpone 2 months',
	'postpone_1_year'	=> 'postpone 1 year',
	'postpone_2_years'	=> 'postpone 2 years',
	'mark_done'			=> 'mark as done',
	'validate'			=> 'validate',
	'archive'			=> 'archive',
	'unarchive'			=> 'unarchive',
	'save_report' 		=> 'save_report',
	'save_task'			=> 'save task',
	'save_and_start'	=> 'save and start'
);
// we're adding these translations to the standard button translations
$GLOBALS['lang']['button'] = $GLOBALS['lang']['button']+$arrButton;