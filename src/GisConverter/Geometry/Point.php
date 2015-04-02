<?php
namespace GisConverter\Geometry;

use GisConverter\Exception;
use GisConverter\Geometry;

class Point extends Geometry\AbstractGeometry {
	const name = "Point";

	private $lon;
	private $lat;

	public function __construct($coords) {
		if (count($coords) < 2) {
			throw new Exception\InvalidFeatureException(__CLASS__, "Point must have two coordinates");
		}
		$lon = $coords[0];
		$lat = $coords[1];
		if (!$this->checkLon($lon)) {
			throw new Exception\OutOfRangeLonException($lon);
		}
		if (!$this->checkLat($lat)) {
			throw new Exception\OutOfRangeLatException($lat);
		}
		$this->lon = (float)$lon;
		$this->lat = (float)$lat;
	}

	public function __get($property) {
		if ($property == "lon") {
			return $this->lon;
		}
		else if ($property == "lat") {
			return $this->lat;
		}
		else {
			throw new \Exception ("Undefined property");
		}
	}

	public function toWKT() {
		return strtoupper(static::name) . "({$this->lon} {$this->lat})";
	}

	public function toKML() {
		return "<" . static::name . "><coordinates>{$this->lon},{$this->lat}</coordinates></" . static::name . ">";
	}

	public function toGPX($mode = null) {
		if (!$mode) {
			$mode = "wpt";
		}
		if ($mode != "wpt") {
			throw new Exception\UnimplementedMethodException(__FUNCTION__, get_called_class());
		}
		return "<wpt lon=\"{$this->lon}\" lat=\"{$this->lat}\"></wpt>";
	}

	public function toGeoJSON() {
		$value = (object)array('type' => static::name, 'coordinates' => array($this->lon, $this->lat));
		return json_encode($value);
	}

	public function equals(GeometryInterface $geom) {
		$isPoint = $geom instanceof Point;
		if (!$isPoint) {
			return false;
		}
		/** @var Point $geom */

		return $geom->getLat() == $this->getLat() && $geom->getLon() == $this->getLon();
	}

	private function checkLon($lon) {
		if (!is_numeric($lon)) {
			return false;
		}
		if ($lon < -180 || $lon > 180) {
			return false;
		}
		return true;
	}

	private function checkLat($lat) {
		if (!is_numeric($lat)) {
			return false;
		}
		if ($lat < -90 || $lat > 90) {
			return false;
		}
		return true;
	}

	/**
	 * @return float
	 */
	public function getLon() {
		return $this->lon;
	}

	/**
	 * @return float
	 */
	public function getLat() {
		return $this->lat;
	}
}