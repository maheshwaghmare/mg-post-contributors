<?php

add_action("mgpc_show_all_social_profile_links", "mgpc_show_all_social_profile_links_init");
function mgpc_show_all_social_profile_links_init() { 
	//  get profiles from array
	global $profiles;
	foreach($profiles as $socialProfile) {
/*		$profileLink = ucfirst($socialProfile);
		array_push($social_links, $profileLink );*/
		echo ucfirst($socialProfile). " - Active<br>";

	}
}


/**
 * 		Add Filter 'mgms_enable_social_profile_links'
 * 
 *	Enable/Disable social profile links at user profile window
 *
 * @return true/false
 * @since 2014
 */
	add_filter('mgms_enable_social_profile_links', 'mgms_enable_social_profile_links');
	function mgms_enable_social_profile_links($status) {
		return $status;
	}



/*$data = apply_filters('mgms_enable_social_profile_links', false);

if($data) {
	echo "TRUE";
} else {
	echo "FALSE";
}*/


?>