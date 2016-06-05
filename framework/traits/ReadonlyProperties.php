<?php

namespace framework\traits;

trait ReadonlyProperties
{
	private $readonlyProperties = [];

	public function __set($name, $value) 
	{
		if (property_exists($this, $name)) {
			if (in_array($name, $this->readonlyProperties)) {
				throw new \Exception("propertity '$name' is readonly.");
			} else {
				$this->name = $value;
				return true;
			}
		} else {
			throw new \OutOfBoundsException("properity '$name' is not exists.");
		}
		
	}

	public function __get($name)
	{
		if (in_array($name, $this->readonlyProperties)) {
			return $this->$name;
		} else {
			throw new \OutOfBoundsException("properity '$name' is not exists.");
		}
	}
}