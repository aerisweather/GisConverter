<?php
namespace GisConverter\Geometry;

use GisConverter\Exception;
use GisConverter\Geometry;

class MultiPoint extends Geometry\GeometryCollection {
	const name = "MultiPoint";

	/** @var Point[] */
	protected $components;

	/**
	 * @param Point[] $components
	 * @throws Exception\InvalidFeatureException
	 */
	public function __construct(array $components) {
		foreach ($components as $comp) {
			if (!($comp instanceof Geometry\Point)) {
				throw new Exception\InvalidFeatureException(__CLASS__, static::name . " can only contain Point elements");
			}
		}
		$this->components = $components;
	}

	public function equals(GeometryInterface $geom) {
		if (!($geom instanceof MultiPoint)) {
			return false;
		}

		/** @var MultiPoint $geom */

		if (count($this->components) != count($geom->getComponents())) {
			return false;
		}
		foreach (range(0, count($this->components) - 1) as $count) {
			if (!$this->components[$count]->equals($geom->getComponents()[$count])) {
				return false;
			}
		}
		return true;
	}

}