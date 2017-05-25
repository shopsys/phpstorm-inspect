<?php

namespace ShopSys\PhpStormInspect;

class OutputPrinter
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    /**
     * @var \ShopSys\PhpStormInspect\ProblemFactory
     */
    private $problemFactory;

    public function __construct(ProblemFactory $problemFactory)
    {
        $this->problemFactory = $problemFactory;
    }

    /**
     * @param string $projectPath
     * @param string $outputPath
     * @return int return code
     */
    public function printOutput($projectPath, $outputPath)
    {
        $outputFiles = glob($outputPath . '/*.xml');

        $problemsByFile = [];

        foreach ($outputFiles as $outputFile) {
            $xml = simplexml_load_file($outputFile);

            $problemsXml = $xml->xpath('/problems/problem');
            foreach ($problemsXml as $problemXml) {
                $problem = $this->problemFactory->create($projectPath, $outputFile, $problemXml);

                $problemsByFile[$problem->filename][] = $problem;
            }
        }

        $this->printProblems($problemsByFile);

        return (count($problemsByFile) > 0) ? self::RETURN_CODE_ERROR : self::RETURN_CODE_OK;
    }

    /**
     * @param \ShopSys\PhpStormInspect\Problem[filename][] $problemsByFile
     */
    private function printProblems(array $problemsByFile)
    {
        ksort($problemsByFile);

        foreach ($problemsByFile as $filename => $problems) {
            $this->sortProblemsByLine($problems);

            printf("File: %s\n", $filename);
            printf("--------------------------------------------------------------------------------\n");
            printf("Found %d problems\n", count($problems));
            printf("--------------------------------------------------------------------------------\n");

            foreach ($problems as $problem) {
                printf(
                    "Line %d: %s: %s\n",
                    $problem->line,
                    $problem->class,
                    $problem->description
                );
            }

            printf("--------------------------------------------------------------------------------\n\n");
        }
    }

    /**
     * @param \ShopSys\PhpStormInspect\Problem[] $problems
     */
    private function sortProblemsByLine(array &$problems)
    {
        usort($problems, function (Problem $problemA, Problem $problemB) {
            return $problemA->line - $problemB->line;
        });
    }
}
