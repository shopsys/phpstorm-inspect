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
use Symfony\Component\Filesystem\Filesystem;

function realpathWithCheck($path) {
	$realpath = realpath($path);
	if ($realpath === false) {
		throw new Exception(sprintf('Path %s not found', $path));
	}

	return $realpath;
}

if ($argc !== 6) {
	echo "Expected 5 arguments:\n";
	echo sprintf(" %s <inspectShExecutableFilepath> <phpstormSystemPath> <projectPath> <inspectionProfileFilepath> <inspectedDirectory>\n", $argv[0]);

	exit(1);
}

$inspectShExecutableFilepath = realpathWithCheck($argv[1]);
$phpstormSystemPath = realpathWithCheck($argv[2]);
$projectPath = realpathWithCheck($argv[3]);
$inspectionProfileFilepath = realpathWithCheck($argv[4]);
$inspectedDirectory = realpathWithCheck($argv[5]);
$outputPath = realpathWithCheck(__DIR__ . '/../output');

$inspectionRunner = new InspectionRunner(new Filesystem());
$inspectionRunner->clearCache($phpstormSystemPath);
$inspectionRunner->clearOutputDirectory($outputPath);
$inspectionRunner->runInspection(
	$inspectShExecutableFilepath,
	$projectPath,
	$inspectionProfileFilepath,
	$outputPath,
	$inspectedDirectory
);

$outputPrinter = new OutputPrinter(new ProblemFactory());
$returnCode = $outputPrinter->printOutput($projectPath, $outputPath);
$inspectionRunner->clearOutputDirectory($outputPath);

exit($returnCode);