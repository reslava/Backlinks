<?php

/**
* Class-Backlinks.php
*
* @package Backlinks
* @link
* @author reslava <reslava@gmail.com>
* @copyright 2023 reslava
* @license https://opensource.org/licenses/MIT The MIT License
*
* @version 0.41
*/

namespace reslava;

if (!defined('SMF'))
	die('No direct access...');

/**
* Show buttons to backlinks of each post
*/
class Backlinks
{
	public function hooks()
	{
		add_integration_function('integrate_prepare_display_context', __CLASS__ . '::prepareDisplayContext#', false, __FILE__);
		add_integration_function('integrate_recent_RecentPosts', __CLASS__ . '::integrate_recent_RecentPosts#', false, __FILE__);
		add_integration_function('integrate_load_theme', __CLASS__ . '::integrateLoadTheme#', false, __FILE__);
	}

	/**
	* @hook integrate_load_theme
	**/
	public function integrateLoadTheme()
	{
		loadCSSFile('backlinks.css');
	}
	/**
	* @hook integrate_recent_RecentPosts
	*/
	public function integrate_recent_RecentPosts()
	{
		global $context;

		foreach ($context['posts'] as $key => $post)
		{
			if (Backlinks::hasBacklink($backlinks, $post['id']))
				Backlinks::addButtons($backlinks, $context['posts'][$key]['quickbuttons']);
		}
	}

	/**
	* @hook integrate_prepare_display_context
	*/
	public function prepareDisplayContext(&$output, &$message, $counter)
	{
		if (Backlinks::hasBacklink($backlinks, $message['id_msg']))
			Backlinks::addButtons($backlinks, $output['quickbuttons']);
	}

	/**
	* add buttons for backlinks
	*/
	public function addButtons($backlinks, &$quickbuttons)
	{
		global $scripturl;

		foreach ($backlinks as $key => $backlink)
		{
			$buttons = array(
				'backlinks' . $backlink[0] => array(
				'label' => '',
				'custom' => 'title="' . $backlink[1] . ' ('. $backlink[2] .')";',
				'href' => $scripturl . '?msg=' . $backlink[0],
				'icon' => 'backlink_button',
				'show' => true,
				)
			);
			$quickbuttons = array_merge($buttons, $quickbuttons);
		};
	}

	/**
	* query to get first 5 backlinks to a given post
	* - backlinks: array of id_msg, subject, poster_name
	* - return true if has any backlink
	*/
	public function hasBacklink(&$backlinks, $id_msg)
	{
		global $smcFunc;

		$results = $smcFunc['db_query']('', '
					SELECT id_msg, subject, poster_name FROM {db_prefix}messages
					WHERE (body REGEXP {string:regex}) 
					LIMIT 5',
					array(
						'regex' => $smcFunc['db_quote'](
							'.*msg=?({int:id_msg}(#{int:id_msg})?([^0-9]|$)).*',
							array(
								'id_msg' => $id_msg
							)
						)
					),
				);				

		if(!$results)
			return false;

		$backlinks = array();
		while ($row = $smcFunc['db_fetch_row']($results))
			$backlinks[] = array($row[0], $row[1], $row[2]);

		$smcFunc['db_free_result']($results);
		if(count($backlinks) == 0)
			return false;

		return true;
	}
}
?>