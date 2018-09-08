<?php
require_once('../setup/setupProcessFile.php');
require_once("../core/php/customCSS.php");
echo loadSentryData($sendCrashInfoJS, $branchSelected); ?>
<script src="../core/js/settings.js?v=<?php echo $cssVersion?>"></script>
<div id="menu">
	<a href="../"> <img class="menuImage" src="<?php echo $baseUrl; ?>img/backArrow.png" style="display: inline-block; cursor: pointer;" height="15px"> </a>
	<?php if(strpos($URI, 'main.php') !== false): ?>
		<a style="cursor: default;" class="active" id="MainLink" >Main</a>
	<?php else: ?>
		<a id="MainLink" onclick="goToUrl('main.php');" >Main</a>
	<?php endif; ?>
	<?php if(strpos($URI, 'testList.php') !== false): ?>
		<a style="cursor: default;" class="active" id="testListLink" >Test List</a>
	<?php else: ?>
		<a id="testListLink" onclick="goToUrl('testList.php');" >Test List</a>
	<?php endif; ?>
	<?php if(strpos($URI, 'update.php') !== false): ?>
		<a style="cursor: default;" class="active" id="updateLink">
	<?php else: ?>
		<a id="updateLink" onclick="goToUrl('update.php');">
	<?php endif; ?>
			<?php if($updateNotificationEnabled === "true")
			{
				if($levelOfUpdate == 1)
				{
					echo '<img id="updateNoticeImage" src="'.$localURL.'img/yellowWarning.png" height="10px">';
				}
				elseif($levelOfUpdate !== 0)
				{
					echo '<img id="updateNoticeImage" src="'.$localURL.'img/redWarning.png" height="10px">';
				}
			}?>
			Update
		</a>
	<?php if(strpos($URI, 'advanced.php') !== false): ?>
		<a style="cursor: default;" class="active" id="AdvancedLink">Advanced</a>
	<?php else: ?>	
		<a id="AdvancedLink" onclick="goToUrl('advanced.php');">Advanced</a>
	<?php endif; ?>
	<?php if(strpos($URI, 'themes.php') !== false): ?>
		<a style="cursor: default;" class="active" id="themesLink">Themes</a>
	<?php else: ?>	
		<a id="themesLink" onclick="goToUrl('themes.php');">Themes</a>
	<?php endif; ?>
	<a id="DevLink"
		<?php if(!(($developmentTabEnabled == 'true') || (strpos($URI, 'devTools.php') !== false))):?>
			style="display: none;
		<?php endif; ?>	
		<?php if(strpos($URI, 'devTools.php') !== false): ?>
			cursor: default;" class="active"
		<?php else: ?>
			" onclick="goToUrl('devTools.php');"
		<?php endif; ?>
	> Dev</a>
</div>

<?php
$baseUrlImages = $localURL;
?>
<script type="text/javascript">
	var baseUrl = "<?php echo $baseUrlImages;?>";
	var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray) ?>');
	var currentVersion = "<?php echo $configStatic['version']; ?>";
	var newestVersion = "<?php echo $configStatic['newestVersion']; ?>";
</script>