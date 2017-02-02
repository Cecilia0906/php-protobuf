<?php

use PHPUnit\Framework\TestCase;
use Basho\Protobuf\Compiler;

class CompilerTest extends TestCase
{
    public function testConstruct()
    {
        $parser = new Compiler();
        $this->assertInstanceOf('Basho\Protobuf\Compiler', $parser);

        $this->assertFalse($parser->getSavePsrOutput());
        $this->assertFalse($parser->isVerbose());
        $this->assertEquals('pb_proto_', $parser->getFilenamePrefix());
        $this->assertEquals('./', $parser->getDestination());

        $this->assertEquals('_', $parser->getNamespaceSeparator());
        $this->assertEquals('Basho_Riak_Api_Pb_Message', $parser->createPackageName('basho.riak.api.pb.message'));
    }

    public function testOptions()
    {
        $parser = new Compiler(true);

        $parser->setSavePsrOutput(true);
        $this->assertTrue($parser->getSavePsrOutput());

        $parser->setFilenamePrefix('a_prefix_');
        $this->assertEquals('a_prefix_', $parser->getFilenamePrefix());

        $parser->setVerbose(true);
        $this->assertTrue($parser->isVerbose());

        $parser->setDestination('~/project/src');
        $this->assertEquals('~/project/src', $parser->getDestination());

        $this->assertEquals('\\', $parser->getNamespaceSeparator());
        $this->assertEquals('Basho\Riak\Api\Pb\Message', $parser->createPackageName('basho.riak.api.pb.message'));
    }

    public function testParse()
    {
        $parser = new Compiler();
        //$parser->parse('tests/test.proto');
    }
}
