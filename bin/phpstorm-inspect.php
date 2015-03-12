<?php

if (file_exists(__DIR__ . '/../../../autoload.php')) {
	// package is installed as dependency of some root project
	require_once __DIR__ . '/../../../autoload.php';
} else {
	require_once __DIR__ . '/../vendor/autoload.php';
}

use ShopSys\PhpStormInspect\InspectionRunner;
use ShopSys\PhpStormInspect\OutputPrinter;
use ShopSys\PhpStormInspect\ProblemFactory;

function realpathWithCheck($path) {
	$realpath = realpath($path);
	if ($realpath === false) {
		throw new Exception(sprintf('Path %s not found', $path));
	}

	return $realpath;
}

if ($argc !== 5) {
	echo "Expected 4 arguments:\n";
	echo sprintf(" %s <inspectShExecutableFilepath> <projectPath> <inspectionProfileFilepath> <inspectedDirectory>\n", $argv[0]);

	exit(1);
}

$inspectShExecutableFilepath = realpathWithCheck($argv[1]);
$projectPath = realpathWithCheck($argv[2]);
$inspectionProfileFilepath = realpathWithCheck($argv[3]);
$inspectedDirectory = realpathWithCheck($argv[4]);
$outputPath = realpathWithCheck(__DIR__ . '/../output');

$inspectionRunner = new InspectionRunner();
$inspectionRunner->cleanOutputDirectory($outputPath);
$inspectionRunner->runInspection(
	$inspectShExecutableFilepath,
	$projectPath,
	$inspectionProfileFilepath,
	$outputPath,
	$inspectedDirectory
);

$outputPrinter = new OutputPrinter(new ProblemFactory());
$returnCode = $outputPrinter->printOutput($outputPath);
$inspectionRunner->cleanOutputDirectory($outputPath);

exit($returnCode);