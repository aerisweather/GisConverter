<?php
namespace GisConverter\Decoder;

use GisConverter\Decoder;
use GisConverter\Exception;
use GisConverter\Geometry\Point;

class GpxDecoder extends Decoder\AbstractXmlDecoder {
	static protected function extractCoordinates(\SimpleXmlElement $xml) {
		$attributes = $xml->attributes();
		$lon = (string)$attributes['lon'];
		$lat = (string)$attributes['lat'];
		if (!$lon or !$lat) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		return array($lon, $lat);
	}

	static protected function parseTrkseg(\SimpleXmlElement $xml) {
		$res = array();
		foreach ($xml->children() as $elem) {
			/** @var \SimpleXmlElement $elem */
			if (strtolower($elem->getName()) == "trkpt") {
				$res[] = new Point(static::extractCoordinates($elem));
			}
		}
		return $res;
	}

	static protected function parseRte(\SimpleXmlElement $xml) {
		$res = array();
		foreach ($xml->children() as $elem) {
			/** @var \SimpleXmlElement $elem */
			if (strtolower($elem->getName()) == "rtept") {
				$res[] = new Point(static::extractCoordinates($elem));
			}
		}
		return $res;
	}

	static protected function parseWpt(\SimpleXmlElement $xml) {
		return static::extractCoordinates($xml);
	}

	static public function geomFromXml(\SimpleXmlElement $xml) {
		$nodename = strtolower($xml->getName());
		if ($nodename == "gpx" or $nodename == "trk") {
			return static::childsCollect($xml);
		}
		foreach (array("Trkseg", "Rte", "Wpt") as $kml_type) {
			if (strtolower($kml_type) == $xml->getName()) {
				$type = $kml_type;
				break;
			}
		}

		if (!isset($type)) {
			throw new Exception\InvalidTextException(__CLASS__);
		}

		try {
			$components = call_user_func(array('static', 'parse' . $type), $xml);
		}
		catch (Exception\InvalidTextException $e) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		catch (\Exception $e) {
			throw $e;
		}

		if ($type == "Trkseg" or $type == "Rte") {
			$constructor = __NAMESPACE__ . '\\' . 'LineString';
		}
		else if ($type == "Wpt") {
			$constructor = __NAMESPACE__ . '\\' . 'Point';
		}
		else {
			throw new \InvalidArgumentException("Unexpected xml document name {$xml->getName()}");
		}

		return new $constructor($components);
	}
}