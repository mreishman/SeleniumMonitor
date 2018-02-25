<?php
$dir = $_POST["dir"];
rename($dir.$_POST["oldName"], $dir.$_POST["newName"]);
echo json_encode(true);