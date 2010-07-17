<?php
/**
 * TaskFreak!
 *
 * @package taskfreak
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.4
 * @copyright GNU General Public License (GPL) version 3
 * 
 * @languagePack Norwegian | Norsk
 * @translator Hans Hovde <hhovde@yahoo.com>
 */

$GLOBALS['lang']['task'] = array(
	'priority'	=> 'prioritet',
	'spent'		=> 'brukt',
	'note'		=> 'notat',
	'todo'		=> 'oppgave',
	'done'		=> 'ferdig',
	'valid'		=> 'gyldig',
	'archived'	=> 'oppnådd',
	'running'	=> 'kjører',
	'paused'	=> 'pauset'
);

$GLOBALS['lang']['priority'] = array(
	'urgent'		=> 'Kritisk',
	'important'		=> 'Høyeste',
	'high'			=> 'Høyere',
	'quickly'		=> 'Høy',
	'pretty_soon'	=> 'Ganske snart',
	'soon'			=> 'Snart',
	'normal'		=> 'Normal',
	'after'			=> 'Lav',
	'later'			=> 'Lavere',
	'low'			=> 'Laverste',
	'anytime'		=> 'Når tid',
	'whenever'		=> 'Uviktig'
);

$GLOBALS['lang']['ui'] = array(
	'reload'		=> 'reload',
	'reload_list'	=> 'reload list',
	'task'			=> 'oppgave',
	'tasks'			=> 'oppgaver',
	'create_single'	=> 'lag en oppgave',
	'create_multi'	=> 'lag flere oppgaver',
	'edit_task'		=> 'endre oppgave',
	'delete_task'	=> 'slett oppgave',
	'start_task'	=> 'start oppgave',
	'mark_archived'	=> 'merk oppgave som arkivert',
	'done_confirm'	=> 'er du sikker på at du vil merke oppgaven som ferdig ?',
	'compact'		=> 'minimer',
	'expand'		=> 'maksimer',
	'select_all'	=> 'velg alle',
	'info'			=> 'info',
	'history'		=> 'historie',
	'history_empty'	=> 'ingen tidsrapporter enda',
	'report_spent'	=> 'rapporter brukt tid',
	'total'			=> 'total',
	'user_menu'		=> 'vis brukermeny',
	'admin'			=> 'admin',
	'settings'		=> 'instillinger',
	'all_users'		=> 'alle brukere',
	'switch'		=> 'bytt',
	'switch_user'	=> 'se andre brukeres oppgaver',
	'select_user'	=> 'velg bruker du vil se oppgaver til',
	'switched'		=> 'bytt til en annen bruker',
	'task_manager'	=> 'oppgave behandling',
	'task_managers'	=> 'oppgave behandlinger',
	'user_admin'	=> 'bruker administrator',
	'user_admins'	=> 'bruker administratorer',
	'last_visit'	=> 'siste besøk',
	'preferences'	=> 'mine instillinger',
	'create_user'	=> 'lag bruker',
	'edit_user'		=> 'endre bruker',
	'delete_user'	=> 'slett bruker'
);

$GLOBALS['lang']['pages'] = array(
	'todo'			=> 'oppgave',
	'done'			=> 'ferdig',
	'valid'			=> 'gyldig',
	'archives'		=> 'arkiv',
	'edit_task'		=> 'endre oppgave',
	'create_task'	=> 'lag oppgave',
	'create_tasks'	=> 'lag flere oppgaver',
	'view_task'		=> 'oppgave detaljer'
);

$arrMess = array(
	'task_created'		=> 'oppgave opprettet',
	'task_updated'		=> 'oppgave oppdatert',
	'%_task_created'	=> '% oppgave(r) opprettet',
	'task_deleted'		=> 'oppgave slettet',
	'time_added'		=> 'arbeidstid registrert'
);
// we're adding these translations to the standard messages translations
$GLOBALS['lang']['message'] = $GLOBALS['lang']['message']+$arrMess;

$arrButton = array(
	'pause'				=> 'pause',
	'resume'			=> 'fortsett',
	'done'				=> 'ferdig',
	'reopen'			=> 'åpne på nytt',
	'postpone_1_day'	=> 'utsett 1 dag',
	'postpone_2_days'	=> 'utsett 2 dager',
	'postpone_1_week'	=> 'utsett 1 uke',
	'postpone_2_weeks'	=> 'utsett 2 uker',
	'postpone_1_month'	=> 'utsett 1 måned',
	'postpone_2_months'	=> 'utsett 2 måneder',
	'postpone_1_year'	=> 'utsett 1 år',
	'postpone_2_years'	=> 'utsett 2 år',
	'mark_done'			=> 'marker som ferdig',
	'validate'			=> 'valider',
	'archive'			=> 'arkiver',
	'unarchive'			=> 'gjenopprett',
	'save_report' 		=> 'lagre rapport',
	'save_task'			=> 'lagre oppgave',
	'save_and_start'	=> 'lagre og start'
);
// we're adding these translations to the standard button translations
$GLOBALS['lang']['button'] = $GLOBALS['lang']['button']+$arrButton;