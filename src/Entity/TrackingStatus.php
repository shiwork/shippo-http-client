<?php

namespace ShippoClient\Entity;

use ShippoClient\Attributes;
use TurmericSpice\ReadableAttributes;

class TrackingStatus extends Entity
{
    use ReadableAttributes {
        mayHaveAsString as public getStatusDetails;
        mayHaveAsString as public getStatusDate;
    }

    /**
     * Date and time of object creation.
     *
     * @return \DateTime
     */
    public function getObjectCreated()
    {
        return $this->attributes->mayHave('object_created')->asInstanceOf('\\DateTime');
    }

    /**
     * Date and time of last object update.
     *
     * @return \DateTime
     */
    public function getObjectUpdated()
    {
        return $this->attributes->mayHave('object_updated')->asInstanceOf('\\DateTime');
    }

    /**
     * Unique identifier of the given object.
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->attributes->mayHave('object_id')->asString();
    }

    /**
     * Package status.
     *  - "UNKNOWN"   The package has not been found via the carrier's tracking system,
     *                or it has been found but not yet scanned by the carrier.
     *  - "TRANSIT"   The package has been scanned by the carrier and is in transit.
     *  - "DELIVERED" The package has been successfully delivered.
     *  - "RETURNED"  The package is en route to be returned to the sender, or has been returned successfully.
     *  - "FAILURE"   The carrier indicated that there has been an issue with the delivery.
     *                This can happen for various reasons and depends on the carrier.
     *                This status does not indicate a technical error, but rather a delivery issue.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->attributes->mayHave('status')->asString();
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        $attributes = $this->attributes->mayHave('location')->asArray();
        return new Location($attributes);
    }
}
