<?php
/**
 * TaskFreak!
 *
 * @package taskfreak
 * @author IST planbar GmbH - www.istplanbar.de (Lars Wohlfahrt, Franziska Hauser)
 * @version 0.4
 * @copyright GNU General Public License (GPL) version 3
 */

$GLOBALS['lang']['task'] = array(
	'priority'	=> 'Priorität',
	'spent'		=> 'aufgewendet',
	'note'		=> 'notieren',
	'todo'		=> 'zu erledigen',
	'done'		=> 'erledigt',
	'valid'		=> 'gültig',
	'archived'	=> 'archiviert',
	'running'	=> 'laufend',
	'paused'	=> 'pausiert'
);

$GLOBALS['lang']['priority'] = array(
	'urgent'		=> 'dringend',
	'important'		=> 'wichtig',
	'high'			=> 'hoch',
	'quickly'		=> 'schnell',
	'pretty_soon'	=> 'sehr bald',
	'soon'			=> 'bald',
	'normal'		=> 'normal',
	'after'			=> 'nach',
	'later'			=> 'später',
	'low'			=> 'niedrig',
	'anytime'		=> 'irgendwann',
	'whenever'		=> 'wann immer'
);

$GLOBALS['lang']['ui'] = array(
	'reload'		=> 'neu laden',
	'reload_list'		=> 'Liste neu laden',
	'task'			=> 'Aufgabe',
	'tasks'			=> 'Aufgaben',
	'create_single'		=> 'Einzelne Aufgabe erstellen',
	'create_multi'		=> 'Mehrere Aufgaben erstellen',
	'edit_task'		=> 'Aufgabe bearbeiten',
	'delete_task'		=> 'Aufgabe löschen',
	'start_task'		=> 'Aufgabe starten',
	'mark_archived'		=> 'Aufgabe als archiviert markieren',
	'done_confirm'		=> 'verschieben und diese Aufgabe wirklich als beendet markieren ?',
	'compact'		=> 'kompakt',
	'expand'		=> 'Ausklappen',
	'select_all'		=> 'Alle auswählen',
	'info'			=> 'Info',
	'history'		=> 'Verlauf',
	'history_empty'		=> 'Bisher noch keine Zeitberichte',
	'report_spent'		=> 'Bericht über Zeitaufwand',
	'total'			=> 'insgesamt',
	'user_menu'		=> 'Benutzermenü anzeigen',
	'admin'			=> 'Admin',
	'settings'		=> 'Einstellungen',
	'all_users'		=> 'Alle Benutzer',
	'switch'		=> 'Wechseln',
	'switch_user'		=> 'Aufgaben anderer Benutzer anzeigen',
	'select_user'		=> 'Wählen Sie den Benutzer, dessen Aufgaben Sie sehen möchten',
	'switched'		=> 'Zu anderem Benutzer gewechselt',
	'task_manager'		=> 'Aufgaben-Manager',
	'task_managers'		=> 'Aufgaben-Manager',
	'user_admin'		=> 'Benutzeradministrator',
	'user_admins'		=> 'Benutzeradministratoren',
	'last_visit'		=> 'Letzter Besuch',
	'preferences'		=> 'Meine Präferenzen',
	'create_user'		=> 'Benutzer erstellen',
	'edit_user'		=> 'Benutzer bearbeiten',
	'delete_user'		=> 'Benutzer löschen'
);

$GLOBALS['lang']['pages'] = array(
	'todo'			=> 'Zu erledigen',
	'done'			=> 'Erledigt',
	'valid'			=> 'Gültig',
	'archives'		=> 'Archiv',
	'edit_task'		=> 'Aufgabe bearbeiten',
	'create_task'		=> 'Aufgabe erstellen',
	'create_tasks'		=> 'Mehrere Aufgaben erstellen',
	'view_task'		=> 'Aufgaben-Details'
);

$arrMess = array(
	'task_created'		=> 'Aufgabe erstellt',
	'task_updated'		=> 'Aufgabe aktualisiert',
	'%_task_created'	=> '% Aufgabe(n) erstellt',
	'task_deleted'		=> 'Aufgabe gelöscht',
	'time_added'		=> 'Berichtete Arbeitszeit'
);

$GLOBALS['lang']['message'] = $GLOBALS['lang']['message']+$arrMess;

$arrButton = array(
	'pause'				=> 'Pause',
	'resume'			=> 'Fortfahren',
	'done'				=> 'Erledigt',
	'reopen'			=> 'Wiedereröffnen',
	'postpone_1_day'		=> 'Um 1 Tag verschieben',
	'postpone_2_days'		=> 'Um 2 Tage verschieben',
	'postpone_1_week'		=> 'Um 1 Woche verschieben',
	'postpone_2_weeks'		=> 'Um 2 Wochen verschieben',
	'postpone_1_month'		=> 'Um 1 Monat verschieben',
	'postpone_2_months'		=> 'Um 2 Monate verschieben',
	'postpone_1_year'		=> 'Um 1 Jahr verschieben',
	'postpone_2_years'		=> 'Um 2 Jahre verschieben',
	'mark_done'			=> 'Als erledigt markieren',
	'validate'			=> 'Überprüfen',
	'archive'			=> 'archivieren',
	'unarchive'			=> 'Archivierung rückgängig machen',
	'save_report' 			=> 'Bericht speichern',
	'save_task'			=> 'Aufgabe speichern',
	'save_and_start'		=> 'Speichern und starten'
);

$GLOBALS['lang']['button'] = $GLOBALS['lang']['button']+$arrButton;
