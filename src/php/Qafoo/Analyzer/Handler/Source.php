<?php

namespace Qafoo\Analyzer\Handler;

use Qafoo\Analyzer\Handler;
use Qafoo\Analyzer\Shell;
use Qafoo\Analyzer\Project;

class Source extends Handler
{
    /**
     * Shell
     *
     * @var Shell
     */
    private $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * Handle provided directory
     *
     * Optionally an existing result file can be provided
     *
     * If a valid file could be generated the file name is supposed to be
     * returned, otherwise return null.
     *
     * @param Project $project
     * @param string $existingResult
     * @return string
     */
    public function handle(Project $project, $existingResult = null)
    {
        $zipFile = __DIR__ . '/../../../../../data/source.zip';
        $archive = new \ZipArchive();
        $archive->open($zipFile, \ZipArchive::OVERWRITE | \ZipArchive::CREATE);
        $finder = new FinderFacade(array($project->baseDir), $project->excludes, array('*.php'));
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $isSeven = true;
        }
        foreach ($finder->findFiles() as $existingResult) {
            if ((is_file($existingResult) && $isSeven) || !$isSeven) {
                $archive->addFile($existingResult, ltrim(str_replace($project->baseDir, '', $existingResult), '/'));
            }
        }
        $archive->close();
    }
}
