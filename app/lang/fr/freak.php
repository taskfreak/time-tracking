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
	'priority'	=> 'priorité',
	'spent'		=> 'durée',
	'note'		=> 'note',
	'todo'		=> 'à faire',
	'done'		=> 'effectué',
	'valid'		=> 'validé',
	'archived'	=> 'archivé',
	'running'	=> 'en cours',
	'paused'	=> 'en pause`'
);

$GLOBALS['lang']['priority'] = array(
	'urgent'		=> 'urgent',
	'important'		=> 'important',
	'high'			=> 'haute',
	'quickly'		=> 'dès que possible',
	'pretty_soon'	=> 'rapidement',
	'soon'			=> 'bientôt',
	'normal'		=> 'normal',
	'after'			=> 'après',
	'later'			=> 'plus tard',
	'low'			=> 'faible',
	'anytime'		=> 'bien plus tard',
	'whenever'		=> 'peu importe'
);

$GLOBALS['lang']['ui'] = array(
	'reload'		=> 'recharger',
	'reload_list'	=> 'recharger la liste',
	'task'			=> 'tâche',
	'tasks'			=> 'tâches',
	'create_single'	=> 'créer une tâche',
	'create_multi'	=> 'créer plusieurs tâches',
	'edit_task'		=> 'modifier tâche',
	'delete_task'	=> 'supprimer tâche',
	'start_task'	=> 'démarrer la tâche',
	'mark_archived'	=> 'marquer comme archivé',
	'done_confirm'	=> 'vraiment marquer comme effectuée?',
	'compact'		=> 'compacte',
	'expand'		=> 'étendue',
	'select_all'	=> 'tout sélectionner',
	'info'			=> 'info',
	'history'		=> 'historique',
	'history_empty'	=> 'aucune durée rapportée',
	'report_spent'	=> 'rapporter une durée passée',
	'total'			=> 'total',
	'user_menu'		=> 'menu utilisateur',
	'admin'			=> 'administration',
	'settings'		=> 'paramètres',
	'all_users'		=> 'tous utilisateurs',
	'switch'		=> 'changer',
	'switch_user'	=> 'changer d\'utilisateur',
	'select_user'	=> 'choisir un utiisateur pour en voir les tâches',
	'switched'		=> 'changement effectué',
	'task_manager'	=> 'chef de projet',
	'task_managers'	=> 'chefs de projet',
	'user_admin'	=> 'administrateur',
	'user_admins'	=> 'administrateurs',
	'last_visit'	=> 'dernière visite',
	'preferences'	=> 'mes préférences',
	'create_user'	=> 'créer utilisateur',
	'edit_user'		=> 'moditier utilisateur',
	'delete_user'	=> 'supprimer utilisateur'
);

$GLOBALS['lang']['pages'] = array(
	'todo'			=> 'tâches à faire',
	'done'			=> 'tâches effectuées',
	'valid'			=> 'tâches validées',
	'archives'		=> 'archives',
	'edit_task'		=> 'modifier une tâche',
	'create_task'	=> 'créer une tâche',
	'create_tasks'	=> 'créer plusieurs tâches',
	'view_task'		=> 'détails de la tâche'
);

$arrMess = array(
	'task_created'		=> 'tâche créée',
	'task_updated'		=> 'tâche mise à jour',
	'%_task_created'	=> '% tâche(s) crées',
	'task_deleted'		=> 'tâche supprimée',
	'time_added'		=> 'temps passé enregistré'
);
// we're adding these translations to the standard messages translations
$GLOBALS['lang']['message'] = $GLOBALS['lang']['message']+$arrMess;

$arrButton = array(
	'pause'				=> 'pause',
	'resume'			=> 'reprendre',
	'done'				=> 'effectuée',
	'reopen'			=> 'réouvrir',
	'postpone_1_day'	=> 'repousser d\'un jour',
	'postpone_2_days'	=> 'repousser de 2 jours',
	'postpone_1_week'	=> 'repousser d\'une semaine',
	'postpone_2_weeks'	=> 'repousser de 2 semaines',
	'postpone_1_month'	=> 'repousser d\'un mois',
	'postpone_2_months'	=> 'repousser de 2 mois',
	'postpone_1_year'	=> 'repousser d\'une année',
	'postpone_2_years'	=> 'repousser de 2 ans',
	'mark_done'			=> 'marquer effectuée',
	'validate'			=> 'valider',
	'archive'			=> 'archiver',
	'unarchive'			=> 'réactiver',
	'save_report' 		=> 'rapporter',
	'save_task'			=> 'enregistrer la tâche',
	'save_and_start'	=> 'enregistrer et démarrer'
);
// we're adding these translations to the standard button translations
$GLOBALS['lang']['button'] = $GLOBALS['lang']['button']+$arrButton;