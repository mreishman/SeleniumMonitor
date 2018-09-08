<?php

$versionCheckArray = array(
	'version'		=> '1.7',
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
		),
		'1.5'	        => array(
			'branchName'	=> '1.5Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added sidebar for viewing the current grid status</li><li>Added option to pass through more config data (server ip, browser, platform)</li><li>Added option to lock cached test results</li></ul></li><li>Bug Fixes<ul><li>Improved poll time for log data collection</li></ul></li></ul>'
		),
		'1.5.1'	        => array(
			'branchName'	=> '1.5.1Update',
			'releaseNotes'	=> '<ul><li>Bug Fixes<ul><li>Fixed style issue with popup on view page</li></ul></li></ul>'
		),
		'1.5.2'	        => array(
			'branchName'	=> '1.5.2Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added refresh button to add test page (to refresh stats for max ajax/browsers)</li></ul></li><li>Bug Fixes<ul><li>Fixed bug with poll requests for sidebar (limited number of active requests to one)</li><li>Fixed issue with input number for max tests per requests (input field)</li></ul></li></ul>'
		),
		'1.5.3'	        => array(
			'branchName'	=> '1.5.3Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added ajax settings to header for running tests</li></ul></li><li>Bug Fixes<ul><li>Fixed styling issue on view past tests page</li></ul></li></ul>'
		),
		'1.6'	        => array(
			'branchName'	=> '1.6Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added overview page</li><li>Added re-run tests option (re runs failed/errored tests if above specified percent)</li><li>Added more view options to view page</li></ul></li></ul>'
		),
		'1.6.1'	        => array(
			'branchName'	=> '1.6.1Update',
			'releaseNotes'	=> '<ul><li>Bug Fixes<ul><li>Added check for log before displaying in overview</li><li>Fixed bug with browser count being not displaying if all nodes were in use</li><li>Fixed bug with internet explore browser count</li><li>Fixed display bug with log width in overview (adding word break)</li></ul></li></ul>'
		),
		'1.6.2'	        => array(
			'branchName'	=> '1.6.2Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Added date next to title on cached test results (if not renamed)</li><li>Moved video to iframe on running tests</li></ul></li><li>Bug Fixes<ul><li>Fixed js issue with sidebar and IE</li><li>Fixed some style issues and popup for viewing nodes</li><li>Fixed image display issue with IE on sidebar & overview page</li></ul></li></ul>'
		),
		'1.6.3'	        => array(
			'branchName'	=> '1.6.3Update',
			'releaseNotes'	=> '<ul><li>Bug Fixes<ul><li>Added refresh next to iframe in tests to refresh if video 404 on first load</li><li>Reduced size of array for generating time estimates. (only from last 100 tests now)</li></ul></li></ul>'
		),
		'1.7'	        => array(
			'branchName'	=> '1.7Update',
			'releaseNotes'	=> '<ul><li>Features<ul><li>Run tests from more than one file at a time</li><li>Seperated info and settings into two panels</li><li>Added ability to add more than one test folder directory</li></ul></li><li>Bug Fixes<ul><li>Removed some unused settings</li><li>Fixed update > 10 bug</li></ul></li></ul>'
		),
	)
);
?>
