<?php
declare(strict_types=1);

namespace Arkitect;

use Symfony\Component\Finder\Finder;

class ClassSet implements \IteratorAggregate
{
    /** @var string[] */
    private $directoryList;

    /** @var array */
    private $exclude;

    private function __construct(string ...$directoryList)
    {
        $this->directoryList = $directoryList;
        $this->exclude = [];
    }

    public function excludePath(string $pattern): self
    {
        $this->exclude[] = Glob::toRegex($pattern);

        return $this;
    }

    public static function fromDir(string ...$directoryList): self
    {
        return new self(...$directoryList);
    }

    public function getDir(): string
    {
        return implode(', ', $this->directoryList);
    }

    public function getIterator(): \Traversable
    {
        $finder = (new Finder())
            ->files()
            ->in($this->directoryList)
            ->name('*.php')
            ->sortByName()
            ->followLinks()
            ->ignoreUnreadableDirs(true)
            ->ignoreVCS(true);

        if ($this->exclude) {
            $finder->notPath($this->exclude);
        }

        return $finder;
    }
}
