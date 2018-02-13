<?php

$returnData = file_get_contents($_POST["path"]);

echo json_encode($returnData);