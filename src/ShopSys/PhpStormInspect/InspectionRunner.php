<?php

namespace ShopSys\PhpStormInspect;

class InspectionRunner {

	public function cleanOutputDirectory($outputPath) {
		$files = glob($outputPath . '/*.xml');

		foreach ($files as $file) {
			unlink($file);
		}
	}

	/**
	 * @param string $inspectShExecutableFilepath
	 * @param string $projectPath
	 * @param string $inspectionProfileFilepath
	 * @param string $outputPath
	 * @param string $inspectedDirectory
	 */
	public function runInspection(
		$inspectShExecutableFilepath,
		$projectPath,
		$inspectionProfileFilepath,
		$outputPath,
		$inspectedDirectory
	) {
		$command = sprintf(
			'%s %s %s %s -d %s 2>&1',
			escapeshellarg($inspectShExecutableFilepath),
			escapeshellarg($projectPath),
			escapeshellarg($inspectionProfileFilepath),
			escapeshellarg($outputPath),
			escapeshellarg($inspectedDirectory)
		);

		$returnCode = null;
		ob_start();
		passthru($command, $returnCode);
		$output = ob_get_clean();

		if ($returnCode !== 0) {
			echo $output;
		}

		return $returnCode;
	}

}
