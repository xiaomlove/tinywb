<?php
namespace framework;

final class App implements \ArrayAccess
{
	private static $instance;

	private static $containers = [];

	private function __construct()
	{

	}

	private function __clone()
	{

	}

	public function getInstance()
	{
		if (self::$instance !== null)
		{
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}

	public function run(array $config)
	{
		
	}

}