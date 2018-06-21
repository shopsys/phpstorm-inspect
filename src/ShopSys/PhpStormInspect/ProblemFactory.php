<?php

namespace ShopSys\PhpStormInspect;

use SimpleXMLElement;

class ProblemFactory
{
    /**
     * @param string $projectPath
     * @param string $xmlFilename
     * @param string $problemXml
     * @return \ShopSys\PhpStormInspect\Problem
     */
    public function create($projectPath, $xmlFilename, $problemXml)
    {
        $problem = new Problem();

        $problem->inspectionName = $this->getInspectionName($xmlFilename);
        $problem->filename = $this->getFilename($projectPath, $problemXml);
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
    private function getInspectionName($xmlFilename)
    {
        return preg_replace('/(.*)\.xml/', '$1', $xmlFilename);
    }

    /**
     * @param string $projectPath
     * @param \SimpleXMLElement $problemXml
     * @return string
     */
    private function getFilename($projectPath, SimpleXMLElement $problemXml)
    {
        $filename = (string)$problemXml->file;
        $filename = preg_replace('#^file://#', '', $filename);
        $filename = str_replace('$PROJECT_DIR$', $projectPath, $filename);

        return realpath($filename);
    }
}
