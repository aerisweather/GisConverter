<?php
namespace GisConverter\Geometry;

use GisConverter\Exception;
use GisConverter\Geometry;

class LinearRing extends LineString {
	const name = "LinearRing";

	/**
	 * @param Point[] $components
	 * @throws Exception\InvalidFeatureException
	 */
	public function __construct($components) {
		$first = $components[0];
		$last = end($components);
		if (!$first->equals($last)) {
			throw new Exception\InvalidFeatureException(__CLASS__, "LinearRing must be closed");
		}
		parent::__construct($components);
	}

	public function contains(GeometryInterface $geom) {
		if ($geom instanceof Geometry\GeometryCollection) {
			foreach ($geom->components as $point) {
				if (!$this->contains($point)) {
					return false;
				}
			}
			return true;
		}
		else if ($geom instanceof Geometry\Point) {
			return $this->containsPoint($geom);
		}
		else {
			throw new Exception\UnimplementedException(get_class($this) . "::" . __FUNCTION__ . " for " . get_class($geom) . " geometry");
		}
	}

	protected function containsPoint(Geometry\Point $point) {
		/*
		 *PHP implementation of OpenLayers.AbstractGeometry.LinearRing.ContainsPoint algorithm
		 */
		$px = round($point->getLon(), 14);
		$py = round($point->getLat(), 14);

		$crosses = 0;
		foreach (range(0, count($this->components) - 2) as $i) {
			$start = $this->components[$i];
			$x1 = round($start->getLon(), 14);
			$y1 = round($start->getLat(), 14);
			$end = $this->components[$i + 1];
			$x2 = round($end->getLon(), 14);
			$y2 = round($end->getLat(), 14);

			if ($y1 == $y2) {
				// horizontal edge
				if ($py == $y1) {
					// point on horizontal line
					if ($x1 <= $x2 && ($px >= $x1 && $px <= $x2) || // right or vert
						$x1 >= $x2 && ($px <= $x1 && $px >= $x2)
					) { // left or vert
						// point on edge
						$crosses = -1;
						break;
					}
				}
				// ignore other horizontal edges
				continue;
			}

			$cx = round(((($x1 - $x2) * $py) + (($x2 * $y1) - ($x1 * $y2))) / ($y1 - $y2), 14);

			if ($cx == $px) {
				// point on line
				if ($y1 < $y2 && ($py >= $y1 && $py <= $y2) || // upward
					$y1 > $y2 && ($py <= $y1 && $py >= $y2)
				) { // downward
					// point on edge
					$crosses = -1;
					break;
				}
			}
			if ($cx <= $px) {
				// no crossing to the right
				continue;
			}
			if ($x1 != $x2 && ($cx < min($x1, $x2) || $cx > max($x1, $x2))) {
				// no crossing
				continue;
			}
			if ($y1 < $y2 && ($py >= $y1 && $py < $y2) || // upward
				$y1 > $y2 && ($py < $y1 && $py >= $y2)
			) { // downward
				$crosses++;
			}
		}
		$contained = ($crosses == -1) ?
			// on edge
			1 :
			// even (out) or odd (in)
			!!($crosses & 1);

		return $contained;
	}

}