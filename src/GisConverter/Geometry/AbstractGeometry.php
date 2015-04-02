<?php
namespace GisConverter\Geometry;


use GisConverter\Exception\UnimplementedMethodException;

abstract class AbstractGeometry implements GeometryInterface {
	const name = "";

	public function toGeoJSON() {
		throw new UnimplementedMethodException(__FUNCTION__, get_called_class());
	}

	public function toKML() {
		throw new UnimplementedMethodException(__FUNCTION__, get_called_class());
	}

	public function toGPX($mode = null) {
		throw new UnimplementedMethodException(__FUNCTION__, get_called_class());
	}

	public function equals(GeometryInterface $geom) {
		throw new UnimplementedMethodException(__FUNCTION__, get_called_class());
	}

	public function __toString() {
		return $this->toWKT();
	}
}