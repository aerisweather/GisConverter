<?php
namespace GisConverter\Geometry;

use GisConverter\Exception;
use GisConverter\Geometry;

class MultiLineString extends Geometry\GeometryCollection {
	const name = "MultiLineString";

	/**
	 * @param LineString[] $components
	 * @throws Exception\InvalidFeatureException
	 */
	public function __construct($components) {
		foreach ($components as $comp) {
			if (!($comp instanceof LineString)) {
				throw new Exception\InvalidFeatureException(__CLASS__, "MultiLineString can only contain LineString elements");
			}
		}
		$this->components = $components;
	}

}