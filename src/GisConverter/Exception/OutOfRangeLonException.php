<?php
namespace GisConverter\Exception;


class OutOfRangeLonException extends AbstractOutOfRangeCoordException {
	public $type = "longitude";
}