<?php

namespace framework;

use framework\traits\ReadonlyProperties;

class Request
{
	use ReadonlyProperties;

	private $server;

	private $isGet;

	private $isPost;

	private $isPut;

	private $isDelete;

	public function __construct()
	{
		$this->server = $_SERVER;
		$this->isGet = $this->getMethod() === 'GET' ? true : false;
	}

	public function getMethod()
	{
		return !empty($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : '';
	}
}