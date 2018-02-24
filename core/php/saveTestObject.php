<?php

//this will take post data and save it to a file in testData folder with the name as timestamp of start of test

$fileName = $_POST["testName"];
$fileContent = $_POST["data"];

file_put_contents("../../tmp/tests/".$fileName.".log", $fileContent);