<?php

namespace prgTW\ErrorHandler\Tests\Processor;

use prgTW\ErrorHandler\ErrorHandler;

class TestProcessorTest extends \PHPUnit_Framework_TestCase
{
	/** @var TestProcessor */
	protected $testProcessor;

	/** @var ErrorHandler */
	protected $errorHandler;

	public function testProcessorsAttached()
	{
		$processors = $this->errorHandler->getProcessorManager()->all();
		$this->assertCount(1, $processors);
		$this->assertEquals($this->testProcessor, $processors[0]);
	}

	public function testProcessorsDetached()
	{
		$processorManager = $this->errorHandler->getProcessorManager();
		$this->assertCount(1, $processorManager->all());
		$this->errorHandler->getProcessorManager()->detach($this->testProcessor);
		$this->assertCount(0, $processorManager->all());
	}

	public function testCallbackExecuted()
	{
		$this->assertFalse($this->testProcessor->getCallbackExecuted());
		$this->errorHandler->handleException(new \Exception());
		$this->assertTrue($this->testProcessor->getCallbackExecuted());
	}

	public function testMetadataAltered()
	{
		$metadata = $this->errorHandler->getMetadata(null, new \Exception());
		$this->assertEquals('app_root_dir', $metadata->getAppRootDir());
		$this->assertEquals('stage', $metadata->getStage());
		$this->assertEquals('app_version', $metadata->getAppVersion());
		$this->assertEquals(array(
			'tag1' => 1,
			'tag2' => 2,
		), $metadata->getTags());
		$this->assertEquals(array(
			'metadatum1' => 1,
			'metadatum2' => 2,
		), $metadata->getMetadata());
		$this->assertEquals(array('category1', 'category2'), $metadata->getCategories());
		$this->assertEquals(array('group1' => 'value2', 'group2' => 'value2'), $metadata->getGrouping());
	}

	public function setUp()
	{
		$this->testProcessor = new TestProcessor();
		$this->errorHandler  = new ErrorHandler();
		$this->errorHandler->getProcessorManager()->attach($this->testProcessor);
	}

	public function tearDown()
	{
		$this->errorHandler->unregister();
	}
}
