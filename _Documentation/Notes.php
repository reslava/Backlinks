<?php

/* 	msg99
	msg=99
	msg99#msg99 yes
	msg99# no
	
	*/






// add_integration_function('integrate_hook_name', __CLASS__ . '::methodName#', false, __FILE__);				
//add_integration_function('integrate_display_message_list', __CLASS__ . '::displayMessageList#', false, __FILE__);
add_integration_function('integrate_load_theme', __CLASS__ . '::integrateLoadTheme#', false, __FILE__);

//ORDER BY `id_msg` ASC
		//LIMIT 5
		
'class' => 'backlink_button_link',

public function prepareDisplayContext(&$output, &$message, $counter)
	{
		global $context, $txt, $scripturl, $user_profile, $modSettings;

public function displayMessageList($messages)
	{		
		global $smcFunc, $context;
		
bdump($messages);
$msgs = implode('|',$messages);
bdump($msgs);
		$context['backlink_messages'] = array();
		$results = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}messages
			WHERE (body REGEXP ".*msg=?(({string:msgs})#?([^0-9]|$)).*")						
			ORDER BY `id_msg` ASC',
			array(																
				'msgs' => $msgs
			)
		);

		while ($row = $smcFunc['db_fetch_row']($results))			
		{			
			bdump($row);
			$context['backlink_messages'][] = $row;
		}
		$smcFunc['db_free_result']($results);
		bdump($context['backlink_messages']);
	}
	
	/**
	 * @hook integrate_load_theme
	 **/	 
	public function integrateLoadTheme()
	{			
		loadLanguage('Backlinks');
		loadTemplate('Backlinks', 'backlinks');
	}		