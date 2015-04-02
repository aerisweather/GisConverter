<?php


namespace GisConverter\Geometry;


use GisConverter\Geometry;
use GisConverter\Exception;

class LineString extends Geometry\MultiPoint {
	const name = "LineString";
	public function __construct($components) {
		if (count ($components) < 2) {
			throw new Exception\InvalidFeatureException(__CLASS__, "LineString must have at least 2 points");
		}
		Geometry\parent::__construct($components);
	}

	public function toKML() {
		return "<" . static::name . "><coordinates>" . implode(" ", array_map(function($comp) {
			return "{$comp->lon},{$comp->lat}";
		}, $this->components)). "</coordinates></" . static::name . ">";
	}

	public function toGPX($mode = null) {
		if (!$mode) {
			$mode = "trkseg";
		}
		if ($mode != "trkseg" and $mode != "rte") {
			throw new Exception\UnimplementedMethodException(__FUNCTION__, get_called_class());
		}
		if ($mode == "trkseg") {
			return '<trkseg>' . implode ("", array_map(function ($comp) {
				return "<trkpt lon=\"{$comp->lon}\" lat=\"{$comp->lat}\"></trkpt>";
			}, $this->components)). "</trkseg>";
		} else {
			return '<rte>' . implode ("", array_map(function ($comp) {
				return "<rtept lon=\"{$comp->lon}\" lat=\"{$comp->lat}\"></rtept>";
			}, $this->components)). "</rte>";
		}
	}

}