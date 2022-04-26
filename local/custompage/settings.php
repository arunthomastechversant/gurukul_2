<?php // Ensure the configurations for this site are set
if ( $hassiteconfig ){
 
	// Create the new settings page
	// - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
	// $settings will be NULL
	$settings = new admin_settingpage( 'local_custompage', 'Home Page Settings' );
 
	// Create 
	$ADMIN->add( 'localplugins', $settings );
 
	// Add a setting field to the settings for this page
	$settings->add( new admin_setting_configtext(
 
		// This is the reference you will use to your configuration
		'local_custompage/seminars',
 
		// This is the friendly title for the config, which will be displayed
		'Number of Seminars:',
 
		// This is helper text for this config field
		'',
 
		// This is the default value
		'',
 
		// This is the type of Parameter this config is
		PARAM_TEXT
 
	) );
	$settings->add( new admin_setting_configtext(
		'local_custompage/participants',
		'Number of Participants:',
		'',
		'',
		PARAM_TEXT
	) );
	$settings->add( new admin_setting_configtext(
		'local_custompage/candidates_placed',
		'Number of Candidates Placed:',
		'',
		'',
		PARAM_TEXT
	) );
	$settings->add( new admin_setting_configtext(
		'local_custompage/candidates_trained',
		'Number of Candidates Trained:',
		'',
		'',
		PARAM_TEXT
    ) );


 
}
?>