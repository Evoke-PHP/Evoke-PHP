<?php
namespace Evoke_Test\Service\Log;

use DateTime,
	Evoke\Service\Log\File,
	PHPUnit_Framework_TestCase,
	org\bovigo\vfs\vfsStream,
	org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class TestWrapper
{
	protected static $callbacks;
	
	public static function setCallback(
		/* String */ $wrapperFunction,
		callable $callback)
	{
		self::$callbacks[$wrapperFunction] = $callback;
	}

	public function mkdir($path, $mode, $options)
	{
		return call_user_func(self::$callbacks['mkdir'],
		                      $path, $mode, $options);
	}
	
	public function stream_metadata($path, $option, $value )
	{
		return call_user_func(self::$callbacks['stream_metadata'],
		                      $path, $option, $value);
	}

	public function url_stat($path, $flags)
	{
		return call_user_func(self::$callbacks['url_stat'], $path, $flags);
	}
}

class FileTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	/*********/
	/* Tests */
	/*********/
	
	/**
	 * Create an object.
	 *
	 * @covers Evoke\Service\Log\File::__construct
	 */
	public function test__Construct()
	{
		$object = new File('Filename');
		$this->assertInstanceOf('Evoke\Service\Log\File', $object);
	}

	/**
	 * Can't make log directory.
	 *
	 * @covers                   Evoke\Service\Log\File::open
	 * @expectedException        RuntimeException
	 * @expectedExceptionMessage Cannot make log directory.
	 */
	public function testCantMakeLogDir()
	{
		$fs = vfsStream::setup('Root', 0000);
				
		$object = new File(vfsStream::url('Root/UNWRITEABLE/log.txt'));
		$object->log(new DateTime, 'Message', 'TEST');
	}
	
	/**
	 * Can't open log file.
	 *
	 * @covers                   Evoke\Service\Log\File::open
	 * @expectedException        RuntimeException
	 * @expectedExceptionMessage Cannot open log file.
	 */
	public function testCantOpenLogFile()
	{
		$fs = vfsStream::setup('Root', 0700);
		vfsStream::newFile('UNOPENABLE', 0700)->at($fs); // for writing.
		$object = new File(vfsStream::url('Root/UNOPENABLE'), 0700, 0400);
		$object->log(new DateTime, 'Message', 'TEST');
	}
	
	/**
	 * Can't chmod log file.
	 *
	 * @covers                   Evoke\Service\Log\File::open
	 * @expectedException        RuntimeException
	 * @expectedExceptionMessage Cannot chmod log file.
	 */
	public function testCantChmodLogFile()
	{
		$fs = vfsStream::setup('Root', 0700);
		vfsStream::newFile('UNCHMODABLE', 0000)
			->chown(1)
			->at($fs);

		$object = new File(vfsStream::url('Root/UNCHMODABLE'),
		                   0700,
		                   0777);
		$object->log(new DateTime, 'Message', 'TEST');
	}

	/**
	 * Can't touch log file.
	 *
	 * @covers                   Evoke\Service\Log\File::open
	 * @expectedException        RuntimeException
	 * @expectedExceptionMessage Cannot touch log file.
	 */
	public function testCantTouchLogFile()
	{
		TestWrapper::setCallback(
			'mkdir',
			function($path, $mode, $options)
			{
				return true;
			});
		
		TestWrapper::setCallback(
			'stream_metadata',
			function($path, $options, $value)
			{
				if ($options == STREAM_META_TOUCH)
				{
					return false;
				}

				return true;
			});

		TestWrapper::setCallback(
			'url_stat',
			function($path, $flags)
			{
				return true;
			});
		
		if (!stream_wrapper_register('test', 'Evoke_Test\Service\Log\TestWrapper'))
		{
			throw new \ErrorException('Cannot register test stream.');
		}
		
		$object = new File('test://log.txt');
		$object->log(new DateTime, 'Message', 'Level');
		
		/*
		$fs = vfsStream::setup('Root', 0700);
		vfsStream::newFile('YOU_CANT_TOUCH_THIS', 0000)->chown(1)->at($fs);
		flock(vfsStream::url('Root/YOU_CANT_TOUCH_THIS'), LOCK_EX);

		$object = new File(vfsStream::url('Root/YOU_CANT_TOUCH_THIS'),
		                   0700,
		                   0777);
		$object->log(new DateTime, 'Message', 'TEST');
		*/
		
	}

	/**
	 * Logging chmods the directory.
	 *
	 * @covers Evoke\Service\Log\File::log
	 * @covers Evoke\Service\Log\File::open
	 */
	public function testChmodDir()
	{
		$fs = vfsStream::setup('LOG_DIR', 0777);
		
		$object = new File(vfsStream::url('LOG_DIR/any.txt'));
		$object->log(new DateTime, 'Msg', 'Lvl');

		// $this->assertSame(0700, $fs->getPermissions());
		$this->assertSame(0640, $fs->getChild('any.txt')->getPermissions());
	}
	
	/**
	 * Logging creates a log file.
	 *
	 * @covers Evoke\Service\Log\File::log
	 * @covers Evoke\Service\Log\File::open
	 */
	public function testLogCreatesFile()
	{
		$fs = vfsStream::setup('LOG_DIR');
		$this->assertFalse($fs->hasChild('log.txt'));
		
		$object = new File(vfsStream::url('LOG_DIR/log.txt'));
		$object->log(new DateTime, 'Message', 'TEST');
		$this->assertTrue($fs->hasChild('log.txt'));
	}

	/**
	 * Logging writes a message to an empty file.
	 *
	 * @covers Evoke\Service\Log\File::log
	 */
	public function testLogToEmpty()
	{
		$fs = vfsStream::setup('Root', null, ['log_filename.whatever']);
		$testDate = new DateTime('25 October 2014 15:00:00');

		$object = new File(vfsStream::url('Root/log_filename.whatever'));
		$object->log($testDate, 'Please be on time.', 'NOTICE');
		
		$this->assertSame(
			$testDate->format('Y-M-d@H:i:sP') . ' [NOTICE] Please be on time.' .
			"\n",
			$fs->getChild('log_filename.whatever')->getContent());
	}

	/**
	 * Logging can write a message without file locking.
	 *
	 * @covers Evoke\Service\Log\File::log
	 */
	public function testWithoutLocking()
	{
		$fs = vfsStream::setup('Root', null, ['l.txt']);
		$testDate = new DateTime('25 October 2014 15:00:00');

		$object = new File(vfsStream::url('Root/l.txt'),
		                   0700,
		                   0640,
		                   false); // Non-Locking
		$object->log($testDate, 'Please be on time.', 'NOTICE');
		
		$this->assertSame(
			$testDate->format('Y-M-d@H:i:sP') . ' [NOTICE] Please be on time.' .
			"\n",
			$fs->getChild('l.txt')->getContent());
	}
	
}
// EOF