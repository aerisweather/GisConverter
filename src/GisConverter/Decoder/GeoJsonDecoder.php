<?php
namespace GisConverter\Decoder;

use GisConverter\Exception;
use GisConverter\Geometry\GeometryInterface;
use GisConverter\Geometry\LinearRing;
use GisConverter\Geometry\LineString;
use GisConverter\Geometry\Point;
use GisConverter\Geometry\Polygon;

class GeoJsonDecoder implements DecoderInterface {

	/**
	 * @param string $text
	 * @return GeometryInterface
	 * @throws Exception\InvalidTextException
	 * @throws \Exception
	 */
	static public function geomFromText($text) {
		$ltext = strtolower($text);
		$obj = json_decode($ltext);
		if (is_null($obj)) {
			throw new Exception\InvalidTextException(__CLASS__, $text);
		}

		try {
			$geom = static::geomFromJson($obj);
		}
		catch (Exception\InvalidTextException $e) {
			throw new Exception\InvalidTextException(__CLASS__, $text);
		}
		catch (\Exception $e) {
			throw $e;
		}

		return $geom;
	}

	/**
	 * @param $json
	 * @return GeometryInterface
	 * @throws Exception\InvalidTextException
	 * @throws \Exception
	 */
	static protected function geomFromJson($json) {
		if (property_exists($json, "geometry") and is_object($json->geometry)) {
			return static::geomFromJson($json->geometry);
		}

		if (!property_exists($json, "type") or !is_string($json->type)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}

		foreach (array(
							 "Point",
							 "MultiPoint",
							 "LineString",
							 "MultiLinestring",
							 "LinearRing",
							 "Polygon",
							 "MultiPolygon",
							 "GeometryGeometryCollection"
						 ) as $json_type) {
			if (strtolower($json_type) == $json->type) {
				$type = $json_type;
				break;
			}
		}

		if (!isset($type)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}

		try {
			$components = call_user_func(array('static', 'parse' . $type), $json);
		}
		catch (Exception\InvalidTextException $e) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		catch (\Exception $e) {
			throw $e;
		}

		$constructor = __NAMESPACE__ . '\\' . $type;
		return new $constructor($components);
	}

	static protected function parsePoint($json) {
		if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		return $json->coordinates;
	}

	static protected function parseMultiPoint($json) {
		if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		return array_map(function ($coords) {
			return new Point($coords);
		}, $json->coordinates);
	}

	static protected function parseLineString($json) {
		return static::parseMultiPoint($json);
	}

	static protected function parseMultiLineString($json) {
		$components = array();
		if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		foreach ($json->coordinates as $coordinates) {
			$linecomp = array();
			foreach ($coordinates as $coord) {
				$linecomp[] = new Point($coord);
			}
			$components[] = new LineString($linecomp);
		}
		return $components;
	}

	static protected function parseLinearRing($json) {
		return static::parseMultiPoint($json);
	}

	static protected function parsePolygon($json) {
		$components = array();
		if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		foreach ($json->coordinates as $polyCoords) {
			$ringcomp = array();
			foreach ($polyCoords as $coord) {
				$ringcomp[] = new Point($coord);
			}
			$components[] = new LinearRing($ringcomp);
		}
		return $components;
	}

	static protected function parseMultiPolygon($json) {
		$components = array();
		if (!property_exists($json, "coordinates") or !is_array($json->coordinates)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		foreach ($json->coordinates as $multiPolyCoords) {
			$polycomp = array();
			foreach ($multiPolyCoords as $polyCoords) {
				$ringcomp = array();
				foreach ($polyCoords as $coord) {
					$ringcomp[] = new Point($coord);
				}
				$polycomp[] = new LinearRing($ringcomp);
			}
			$components[] = new Polygon($polycomp);
		}
		return $components;
	}

	/**
	 * @param $json
	 * @return GeometryInterface[]
	 * @throws Exception\InvalidTextException
	 * @throws \Exception
	 */
	static protected function parseGeometryCollection($json) {
		if (!property_exists($json, "geometries") or !is_array($json->geometries)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		$components = array();
		foreach ($json->geometries as $geometry) {
			$components[] = static::geomFromJson($geometry);
		}

		return $components;
	}

}