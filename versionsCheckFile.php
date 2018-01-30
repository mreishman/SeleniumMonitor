<?php

$versionCheckArray = array(
	'version'		=> '1.2',
	'versionList'		=> array(
		'1.0'	        => array(
			'branchName'	=> '1.0Update',
			'releaseNotes'	=> '<ul><li>Initial Commit</li></ul>'
		),
		'1.1'	        => array(
			'branchName'	=> '1.1Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added ability to change website pointing to while running</li><li>Website input keeps data after starting to run a new test</li><li>Better disconnected interface</li><li>Added custom default base url</li><li>Option to re-run tests</li><li>Added FAQ Page</li><li>Added more test information to running test dropdown menu</li><li>Added check if node is open before starting to run a new test</li></ul></li><li>Bug Fixes<ul><li>Updated browser icons</li><li>Added custom timeout vars for data requests</li><li>Fixed issue with php unit verify poll not working</li></ul></li></ul>'
		),
		'1.2'	        => array(
			'branchName'	=> '1.2Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added popup for view when clicking on computer view</li><li>Reduced number of ajax requests to improve refresh rate</li><li>Added ETA to running tests</li><li>Clicking on percent of tests shows fraction value</li><li>Changed up menu navigation, for easier expanding in future</li></ul></li><li>Bug Fixes<ul><li>Fixed bug with enabeling devtools</li><li>Hides files in dropdown that do not contain tests</li><li>Added edit option for main server ip address in settings page</li></ul></li></ul>'
		)
	)
);
?>
