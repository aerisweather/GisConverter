<?php

namespace GisConverter\Decoder;

use GisConverter\Exception;
use GisConverter\Geometry\GeometryGeometryCollection;

abstract class AbstractXmlDecoder implements XmlDecoderInterface {
	static public function geomFromText($text) {
		if (!function_exists("simplexml_load_string") || !function_exists("libxml_use_internal_errors")) {
			throw new Exception\UnavailableResourceException("simpleXML");
		}
		libxml_use_internal_errors(true);
		$xmlobj = simplexml_load_string($text);
		if ($xmlobj === false) {
			throw new Exception\InvalidTextException(__CLASS__, $text);
		}

		try {
			$geom = static::geomFromXml($xmlobj);
		}
		catch (Exception\InvalidTextException $e) {
			throw new Exception\InvalidTextException(__CLASS__, $text);
		}
		catch (\Exception $e) {
			throw $e;
		}

		return $geom;
	}

	static protected function childElements(\SimpleXMLElement $xml, $nodename = "") {
		$nodename = strtolower($nodename);
		$res = array();
		foreach ($xml->children() as $child) {
			/** @var \SimpleXmlElement $child */
			if ($nodename) {
				if (strtolower($child->getName()) == $nodename) {
					array_push($res, $child);
				}
			}
			else {
				array_push($res, $child);
			}
		}
		return $res;
	}

	static protected function childsCollect(\SimpleXMLElement $xml) {
		$components = array();
		foreach (static::childElements($xml) as $child) {
			try {
				$geom = static::geomFromXml($child);
				$components[] = $geom;
			}
			catch (Exception\InvalidTextException $e) {
			}
		}

		$ncomp = count($components);
		if ($ncomp == 0) {
			throw new Exception\InvalidTextException(__CLASS__);
		}
		else if ($ncomp == 1) {
			return $components[0];
		}
		else {
			return new GeometryGeometryCollection($components);
		}
	}
}