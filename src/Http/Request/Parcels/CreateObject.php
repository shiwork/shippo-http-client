<?php

namespace ShippoClient\Http\Request\Parcels;

use ShippoClient\Attributes;
use ShippoClient\Attributes\InvalidAttributeException;

class CreateObject
{
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = new Attributes($attributes);
    }

    /**
     * First dimension of the Parcel.
     * The length should always be the largest of the three dimensions length, width and height;
     * our API will automatically order them if this is not the case.
     * Up to six digits in front and four digits after the decimal separator are accepted.
     *
     * @return int
     * @throws InvalidAttributeException
     */
    public function getLength()
    {
        return $this->attributes->mustHave('length')->asInteger();
    }

    /**
     * Second dimension of the Parcel.
     * The width should always be the second largest of the three dimensions length, width and height;
     * our API will automatically order them if this is not the case.
     * Up to six digits in front and four digits after the decimal separator are accepted.
     *
     * @return int
     * @throws InvalidAttributeException
     */
    public function getWidth()
    {
        return $this->attributes->mustHave('width')->asInteger();
    }

    /**
     * Third dimension of the parcel.
     * The height should always be the smallest of the three dimensions length, width and height;
     * our API will automatically order them if this is not the case.
     * Up to six digits in front and four digits after the decimal separator are accepted.
     *
     * @return int
     * @throws InvalidAttributeException
     */
    public function getHeight()
    {
        return $this->attributes->mustHave('height')->asInteger();
    }

    /**
     * The unit used for length, width and height.
     *
     * @return string
     * @throws InvalidAttributeException
     */
    public function getDistanceUnit()
    {
        return $this->attributes->mustHave('distance_unit')->asString(function($distanceUnit) {
            return in_array($distanceUnit, array('cm', 'in', 'ft', 'mm', 'm', 'yd'));
        });
    }

    /**
     * Weight of the parcel. Up to six digits in front and four digits after the decimal separator are accepted.
     *
     * @return int
     * @throws InvalidAttributeException
     */
    public function getWeight()
    {
        return $this->attributes->mustHave('weight')->asInteger();
    }

    /**
     * The unit used for weight.
     *
     * @return string
     * @throws InvalidAttributeException
     */
    public function getMassUnit()
    {
        return $this->attributes->mustHave('mass_unit')->asString(function($massUnit) {
            return in_array($massUnit, array('g', 'oz', 'lb', 'kg'));
        });
    }

    /**
     * A parcel template is a predefined package used by one or multiple carriers.
     * See the table below for all available values and the corresponding tokens.
     * When a template is given, the parcel dimensions have to be sent, but will not be used for the Rate generation.
     * The dimensions below will instead be used. The parcel weight is not affected by the use of a template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->attributes->mayHave('template')->asString();
    }

    /**
     * A string of up to 100 characters that can be filled with any additional information you want to attach to the object.
     *
     * @return string
     */
    public function getMetadata()
    {
        return $this->attributes->mayHave('metadata')->asString(function($metadata) {
//            return mb_strlen($metadata) <= 100;
            return strlen($metadata) <= 100;
        });
    }

    public function toArray()
    {
        return array_filter(array(
            'length'        => $this->getLength(),
            'width'         => $this->getWidth(),
            'height'        => $this->getHeight(),
            'distance_unit' => $this->getDistanceUnit(),
            'weight'        => $this->getWeight(),
            'mass_unit'     => $this->getMassUnit(),
            'template'      => $this->getTemplate(),
            'metadata'      => $this->getMetadata()
        ));
    }
}