<?php

if (file_exists(__DIR__ . '/../../../autoload.php')) {
    // package is installed as dependency of some root project
    require_once __DIR__ . '/../../../autoload.php';
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Mutex;
use ShopSys\PhpStormInspect\InspectionRunner;
use ShopSys\PhpStormInspect\OutputPrinter;
use ShopSys\PhpStormInspect\ProblemFactory;
use Symfony\Component\Filesystem\Filesystem;

function realpathWithCheck($path)
{
    $realpath = realpath($path);
    if ($realpath === false) {
        throw new \Exception(sprintf('Path %s not found', $path));
    }

    return $realpath;
}

try {
    if ($argc !== 6) {
        throw new \Exception(sprintf("Expected 5 arguments:\n"
            . "%s <inspectShExecutableFilepath> <phpstormSystemPath> <projectPath> <inspectionProfileFilepath> <inspectedDirectory>\n", $argv[0]));
    }

    $inspectShExecutableFilepath = realpathWithCheck($argv[1]);
    $phpstormSystemPath = realpathWithCheck($argv[2]);
    $projectPath = realpathWithCheck($argv[3]);
    $inspectionProfileFilepath = realpathWithCheck($argv[4]);
    $inspectedDirectory = realpathWithCheck($argv[5]);
    $outputPath = realpathWithCheck(__DIR__ . '/../output');

    $lock = new FlockLock(sys_get_temp_dir());
    $mutex = new Mutex('phpstorm-inspect', $lock);

    if (!$mutex->acquireLock(2*3600*1000)) {
        throw new \Exception('Could not acquire lock');
    }

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

    $mutex->releaseLock();

    exit($returnCode);
} catch (\Exception $ex) {
    echo $ex->getMessage() . "\n";

    exit(1);
}
