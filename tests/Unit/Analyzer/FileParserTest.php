<?php

declare(strict_types=1);

namespace Arkitect\Tests\Unit\Analyzer;

use Arkitect\Analyzer\FileParser;
use Arkitect\Analyzer\FileVisitor;
use Arkitect\Analyzer\NameResolver;
use Arkitect\CLI\TargetPhpVersion;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
    public function test_parse_file(): void
    {
        $traverser = $this->createMock(NodeTraverser::class);
        $fileVisitor = $this->createMock(FileVisitor::class);
        $nameResolver = $this->createMock(NameResolver::class);

        $traverser->addVisitor($nameResolver);
        $traverser->addVisitor($fileVisitor);

        $fileVisitor->expects($this->once())->method('clearParsedClassDescriptions');

        $fileParser = new FileParser(
            $traverser,
            $fileVisitor,
            $nameResolver,
            TargetPhpVersion::create('7.4')
        );

        $content = '<?php
        class Foo {}
        ';

        $traverser->expects($this->once())->method('traverse')->with($this->isType('array'));
        $fileParser->parse($content, 'foo');
    }

    /**
     * @requires PHP < 8.0
     */
    public function test_parse_file_with_name_match(): void
    {
        $traverser = $this->createMock(NodeTraverser::class);
        $fileVisitor = $this->createMock(FileVisitor::class);
        $nameResolver = $this->createMock(NameResolver::class);

        $traverser->addVisitor($nameResolver);
        $traverser->addVisitor($fileVisitor);

        $fileVisitor->expects($this->once())->method('clearParsedClassDescriptions');

        $fileParser = new FileParser(
            $traverser,
            $fileVisitor,
            $nameResolver,
            TargetPhpVersion::create('7.4')
        );

        $content = '<?php
        class Match {}
        ';

        $traverser->expects($this->once())->method('traverse')->with($this->isType('array'));
        $fileParser->parse($content, 'foo');
    }
}
