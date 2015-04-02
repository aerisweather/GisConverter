<?php
namespace GisConverter\Geometry;

use GisConverter\Exception;
use GisConverter\Geometry;

class MultiPolygon extends Geometry\GeometryCollection {
	const name = "MultiPolygon";

	/**
	 * @param Polygon[] $components
	 * @throws Exception\InvalidFeatureException
	 */
	public function __construct($components) {
		foreach ($components as $comp) {
			if (!($comp instanceof Polygon)) {
				throw new Exception\InvalidFeatureException(__CLASS__, "MultiPolygon can only contain Polygon elements");
			}
		}
		$this->components = $components;
	}
}