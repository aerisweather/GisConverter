<?php


namespace GisConverter\Decoder;


use GisConverter\Geometry\GeometryInterface;

interface XmlDecoderInterface extends DecoderInterface {

	/**
	 * @param \SimpleXMLElement $xml
	 * @return GeometryInterface
	 */
	static public function geomFromXml(\SimpleXMLElement $xml);

}