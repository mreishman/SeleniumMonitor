<?php
$fileContent = highlight_file($_POST['file']);
echo json_encode($fileContent);