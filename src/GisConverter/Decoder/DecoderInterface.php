<?php
namespace GisConverter\Decoder;

use GisConverter\Geometry\GeometryInterface;

interface DecoderInterface {
	/**
	 * @param string $text
	 * @return GeometryInterface
	 */
	static public function geomFromText($text);
}