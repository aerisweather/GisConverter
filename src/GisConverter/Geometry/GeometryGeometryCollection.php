<?php


namespace GisConverter\Geometry;


use GisConverter\Exception;
use GisConverter\Geometry;

class GeometryGeometryCollection extends Geometry\GeometryCollection {
    const name = "GeometryGeometryCollection";

    public function __construct($components) {
        foreach ($components as $comp) {
            if (!($comp instanceof Geometry\AbstractGeometry)) {
                throw new Exception\InvalidFeatureException(__CLASS__, "GeometryGeometryCollection can only contain AbstractGeometry elements");
            }
        }
        $this->components = $components;
    }

    public function toWKT() {
        return strtoupper(static::name) . "(" . implode(',', array_map(function (GeometryInterface $comp) {
            return $comp->toWKT();
        }, $this->components)) . ')';
    }

    public function toGeoJSON() {
        $value = (object)array ('type' => static::name, 'geometries' =>
            array_map(function (GeometryInterface $comp) {
                // XXX: quite ugly
                return json_decode($comp->toGeoJSON());
            }, $this->components)
        );
        return json_encode($value);
    }
}

?>
