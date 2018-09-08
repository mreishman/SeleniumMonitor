<?php
require_once('../setup/setupProcessFile.php');
require_once("../core/php/customCSS.php");
echo loadSentryData($sendCrashInfoJS, $branchSelected); ?>
<script src="../core/js/settings.js?v=<?php echo $cssVersion?>"></script>
<div id="menu">
	<a href="../"> <img class="menuImage" src="<?php echo $baseUrl; ?>img/backArrow.png" style="display: inline-block; cursor: pointer;" height="15px"> </a>
	<?php if(strpos($URI, 'about.php') !== false): ?>
		<a style="cursor: default;" class="active" id="aboutLink" >About</a>
	<?php else: ?>
		<a id="aboutLink" onclick="goToUrl('about.php');">About</a>
	<?php endif; ?>
	<?php if(strpos($URI, 'faq.php') !== false): ?>
		<a style="cursor: default;" class="active" id="faqLink" >FAQ</a>
	<?php else: ?>
		<a id="faqLink" onclick="goToUrl('faq.php');">FAQ</a>
	<?php endif; ?>
	<?php if(strpos($URI, 'changeLog.php') !== false): ?>
		<a style="cursor: default;" class="active" id="changelogLink" >Changelog</a>
	<?php else: ?>
		<a id="changelogLink" onclick="goToUrl('changeLog.php');">Changelog</a>
	<?php endif; ?>
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