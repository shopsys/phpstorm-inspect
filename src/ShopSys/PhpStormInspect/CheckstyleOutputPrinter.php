<?php

namespace ShopSys\PhpStormInspect;

class CheckstyleOutputPrinter extends OutputPrinter
{
    /**
     * @param Problem[][] $problemsByFile
     */
    protected function printProblems(array $problemsByFile)
    {
        ksort($problemsByFile);

        $report = new \SimpleXMLElement('<checkstyle/>');
        $report->addAttribute('version', '1.0.0');

        foreach ($problemsByFile as $filename => $problems) {
            $this->sortProblemsByLine($problems);

            $file = $report->addChild('file');
            $file->addAttribute('name', $filename);

            foreach ($problems as $problem) {
                $error = $file->addChild('error');
                $error->addAttribute('line', $problem->line);
                $error->addAttribute('column', 0);
                $error->addAttribute('severity', strtolower($problem->severity));
                $error->addAttribute('message', $problem->description);
            }
        }

        $document = dom_import_simplexml($report)->ownerDocument;
        $document->formatOutput = true;

        echo $document->saveXML();
    }
}
