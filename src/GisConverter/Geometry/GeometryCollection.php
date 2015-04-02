<?php

namespace GisConverter\Geometry;

use GisConverter\Geometry;

abstract class GeometryCollection extends Geometry\AbstractGeometry {
	/** @var GeometryInterface[] */
	protected $components;

	public function __get($property) {
		if ($property == "components") {
			return $this->components;
		}
		else {
			throw new \Exception ("Undefined property");
		}
	}

	public function toWKT() {
		$recursiveWKT = function ($geom) use (&$recursiveWKT) {
			if ($geom instanceof Geometry\Point) {
				return "{$geom->getLon()} {$geom->getLat()}";
			}
			else {
				return "(" . implode(',', array_map($recursiveWKT, $geom->components)) . ")";
			}
		};
		return strtoupper(static::name) . call_user_func($recursiveWKT, $this);
	}

	public function toGeoJSON() {
		$recursiveJSON = function ($geom) use (&$recursiveJSON) {
			if ($geom instanceof Geometry\Point) {
				return array($geom->getLon(), $geom->getLat());
			}
			else {
				return array_map($recursiveJSON, $geom->components);
			}
		};
		$value = (object)array('type' => static::name, 'coordinates' => call_user_func($recursiveJSON, $this));
		return json_encode($value);
	}

	public function toKML() {
		return '<MultiGeometry>' . implode("", array_map(function (GeometryInterface $comp) {
			return $comp->toKML();
		}, $this->components)) . '</MultiGeometry>';
	}

	/**
	 * @return GeometryInterface[]
	 */
	public function getComponents() {
		return $this->components;
	}

}