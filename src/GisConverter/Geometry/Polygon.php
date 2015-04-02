<?php
namespace GisConverter\Geometry;

use GisConverter\Exception;
use GisConverter\Geometry;

class Polygon extends Geometry\GeometryCollection {
	const name = "Polygon";

	/**
	 * @param LinearRing[] $components
	 * @throws Exception\InvalidFeatureException
	 */
	public function __construct(array $components) {
		$outer = $components[0];
		foreach (array_slice($components, 1) as $inner) {
			if (!$outer->contains($inner)) {
				throw new Exception\InvalidFeatureException(__CLASS__, "Polygon inner rings must be enclosed in outer ring");
			}
		}
		foreach ($components as $comp) {
			if (!($comp instanceof Geometry\LinearRing)) {
				throw new Exception\InvalidFeatureException(__CLASS__, "Polygon can only contain LinearRing elements");
			}
		}
		$this->components = $components;
	}

	public function toKML() {
		$str = '<outerBoundaryIs>' . $this->components[0]->toKML() . '</outerBoundaryIs>';
		$str .= implode("", array_map(function (GeometryInterface $comp) {
			return '<innerBoundaryIs>' . $comp->toKML() . '</innerBoundaryIs>';
		}, array_slice($this->components, 1)));
		return '<' . static::name . '>' . $str . '</' . static::name . '>';
	}

}