<?php
namespace GisConverter\Geometry;


interface GeometryInterface {
	/*
	 * @return string
	 */
	public function toGeoJSON();

	/*
	 * @return string
	 */
	public function toKML();

	/*
	 * @return string
	 */
	public function toWKT();

	/*
	 * @param mode: trkseg, rte or wpt
	 * @return string
	 */
	public function toGPX($mode = null);

	/*
	 * @param AbstractGeometry $geom
	 * @return boolean
	 */
	public function equals(GeometryInterface $geom);
}