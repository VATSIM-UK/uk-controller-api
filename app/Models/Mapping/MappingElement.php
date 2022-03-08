<?php

namespace App\Models\Mapping;

use Location\Coordinate;

interface MappingElement
{
    /**
     * The id that uniquely identifies the element within its
     * type.
     */
    public function elementId(): int;

    /**
     * The type of the element. e.g. visual_reference_point
     */
    public function elementType(): string;

    /**
     * The name of the element, for labels etc
     */
    public function elementName(): string;

    /**
     * The coordinate of the element.
     */
    public function elementCoordinate(): Coordinate;
}
