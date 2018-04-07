<?php

$versionCheckArray = array(
	'version'		=> '1.4',
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
		),
		'1.3'	        => array(
			'branchName'	=> '1.3Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added cache for tests (when running tests, the output is stored as a cache file)</li><li>View other running tests (cache files are generated, viewable on view > tests page)</li><li>Compare previous cached tests from compare page</li><li>Option to show tests from folders within main folder</li><li>Either shows video names as time or session ID</li></ul></li><li>Bug Fixes<ul><li>Does not show .temp video files until complete</li></ul></li></ul>'
		),
		'1.3.1'	        => array(
			'branchName'	=> '1.3.1Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Shows count next to groups for new tests</li><li>Added link to test log when running tests</li></ul></li><li>Bug Fixes<ul><li>Test cache display fixes (for when cache saved incorrectly)</li></ul></li></ul>'
		),
		'1.4'	        => array(
			'branchName'	=> '1.4Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Changed progress bar for running tests to reflect errors, pass and fails</li><li>Added tabs at bottom of test window for better seperation of data</li><li>Added video link to tests (for each test, in video tab)</li><li>Added log link to tests (for each test, in log tab)</li></ul></li><li>Bug Fixes<ul><li>Fixed issue with save of eta view not showing up after save (requires re-save to show up again)</li></ul></li></ul>'
		)
	)
);
?>