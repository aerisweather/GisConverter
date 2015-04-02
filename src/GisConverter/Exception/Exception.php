<?php
namespace GisConverter\Exception;

abstract class Exception extends \Exception {
	protected $message;

	public function __toString() {
		return get_class($this) . " {$this->message} in {$this->file}({$this->line})\n{$this->getTraceAsString()}";
	}
}