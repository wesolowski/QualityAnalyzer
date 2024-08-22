<?php declare(strict_types=1);

namespace Qafoo\Analyzer\Handler;

use Symfony\Component\Finder\Finder;

final class FinderFacade
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @var array
     */
    private $excludes = [];

    /**
     * @var array
     */
    private $names = [];

    /**
     * @var array
     */
    private $notNames = [];

    /**
     * @var array
     */
    private $regularExpressionsExcludes = [];

    public function __construct(array $items = [], array $excludes = [], array $names = [], array $notNames = [], $regularExpressionsExcludes = [])
    {
        $this->items                      = $items;
        $this->excludes                   = $excludes;
        $this->names                      = $names;
        $this->notNames                   = $notNames;
        $this->regularExpressionsExcludes = $regularExpressionsExcludes;
    }

    /**
     * @return string[]
     */
    public function findFiles(): array
    {
        $files   = [];
        $finder  = new Finder;
        $iterate = false;

        $finder->ignoreUnreadableDirs();
        $finder->sortByName();

        foreach ($this->items as $item) {
            if (!\is_file($item)) {
                $finder->in($item);
                $iterate = true;
            } else {
                $files[] = \realpath($item);
            }
        }

        foreach ($this->excludes as $exclude) {
            $finder->exclude($exclude);
        }

        foreach ($this->names as $name) {
            $finder->name($name);
        }

        foreach ($this->notNames as $notName) {
            $finder->notName($notName);
        }

        foreach ($this->regularExpressionsExcludes as $regularExpressionExclude) {
            $finder->notPath($regularExpressionExclude);
        }

        if ($iterate) {
            foreach ($finder as $file) {
                $files[] = $file->getRealpath();
            }
        }

        return $files;
    }
}
