<?php

use PHPUnit\Framework\TestCase;
use Basho\Protobuf\Compiler\ProtobufParser;

class ProtobufParserTest extends TestCase
{
    public function testConstruct()
    {
        $parser = new ProtobufParser();
        $this->assertInstanceOf('Basho\Protobuf\Compiler\ProtobufParser', $parser);
    }
}
