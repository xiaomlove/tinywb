<?php

namespace framework\traits;

trait ReadonlyProperties
{
	private $readonlyProperties = [];

	public function __set($name, $value) 
	{
		if (property_exists($this, $name)) {
			if (in_array($name, $this->readonlyProperties)) {
				trigger_error('属性只读');
				return false;
			} else {
				$this->name = $value;
				return true;
			}
		} else {
			trigger_error("不存在该属：$name");
			return false;
		}
		
	}

	public function __get($name)
	{
		if (in_array($name, $this->readonlyProperties)) {
			return $this->$name;
		} else {
			trigger_error("不存在该属：$name");
		}
	}
}