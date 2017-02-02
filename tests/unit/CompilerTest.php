<?php

use PHPUnit\Framework\TestCase;
use Basho\Protobuf\Compiler;

class CompilerTest extends TestCase
{
    public function testConstruct()
    {
        $parser = new Compiler();
        $this->assertInstanceOf('Basho\Protobuf\Compiler', $parser);
    }
}
