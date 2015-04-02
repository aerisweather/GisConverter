<?php
namespace GisConverter\Exception;


class OutOfRangeLatException extends AbstractOutOfRangeCoordException {
	public $type = "latitude";
}