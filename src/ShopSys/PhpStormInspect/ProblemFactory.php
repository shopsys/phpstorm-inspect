<?php

namespace ShopSys\PhpStormInspect;

class ProblemFactory {

	/**
	 * @param string $xmlFilename
	 * @param string $problemXml
	 * @return \ShopSys\PhpStormInspect\Problem
	 */
	public function create($xmlFilename, $problemXml) {
		$problem = new Problem();

		$problem->inspectionName = $this->getInspectionName($xmlFilename);
		$problem->filename = $this->getFilename($problemXml);
		$problem->line = (int)$problemXml->line;
		$problem->class = (string)$problemXml->problem_class;
		$problem->severity = (string)$problemXml->problem_class['severity'];
		$problem->description = (string)$problemXml->description;

		return $problem;
	}

	/**
	 * @param string $xmlFilename
	 * @return string
	 */
	private function getInspectionName($xmlFilename) {
		return preg_replace('/(.*)\.xml/', '$1', $xmlFilename);
	}

	/**
	 * @param string $problemXml
	 * @return string
	 */
	private function getFilename($problemXml) {
		return str_replace('file://$PROJECT_DIR$/', '', (string)$problemXml->file);
	}

}
