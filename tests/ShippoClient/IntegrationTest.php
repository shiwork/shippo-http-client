<?php

namespace ShippoClient;

/**
 * ugly and helpful test class
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    private static $accessToken = null;

    public function setUp()
    {
        self::$accessToken = getenv('SHIPPO_PRIVATE_ACCESS_TOKEN');
    }

    /**
     * @test
     * @return array
     */
    public function createAddress()
    {
        $this->assertNotFalse(self::$accessToken, 'You should set env SHIPPO_PRIVATE_ACCESS_TOKEN.');

        $param = array(
            "object_purpose" => "PURCHASE",
            "name"           => "Address From User",
            "company"        => "Shippo",
            "street1"        => "215 Clayton St.",
            "street2"        => "",
            "city"           => "San Francisco",
            "state"          => "CA",
            "zip"            => "94117",
            "country"        => "US",
            "phone"          => "+1 555 341 9393",
            "email"          => "api@goshippo.com",
            "is_residential" => true,
            "metadata"       => "integration test"
        );
        $addressFrom = ShippoClient::provider(self::$accessToken)->addresses()->create($param);
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Addresses\\Address', $addressFrom);

        $addressFromArray = $addressFrom->toArray();
        $this->assertSame('VALID', $addressFromArray['object_state']);
        $this->assertSame('FULLY_ENTERED', $addressFromArray['object_source']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/', $addressFromArray['object_created']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/', $addressFromArray['object_updated']);
        $this->assertNotEmpty($addressFromArray['object_id']);
        $this->assertNotEmpty($addressFromArray['object_owner']);
        $this->assertSame('Address From User', $addressFromArray['name']);
        $this->assertSame('Shippo', $addressFromArray['company']);
        $this->assertSame("", $addressFromArray['street_no']);
        $this->assertSame("215 Clayton St.", $addressFromArray['street1']);
        $this->assertSame("", $addressFromArray['street2']);
        $this->assertSame("San Francisco", $addressFromArray['city']);
        $this->assertSame("CA", $addressFromArray['state']);
        $this->assertSame("94117", $addressFromArray['zip']);
        $this->assertSame("US", $addressFromArray['country']);
        $this->assertSame("0015553419393", $addressFromArray['phone']); // casted
        $this->assertSame("api@goshippo.com", $addressFromArray['email']); // casted
        $this->assertTrue($addressFromArray['is_residential']);
        $this->assertSame("", $addressFromArray['ip']);
        $this->assertInternalType('array', $addressFromArray['messages']);
        $this->assertSame("integration test", $addressFromArray['metadata']);

        $param = array(
            "object_purpose" => "PURCHASE",
            "name"           => "Address To User",
            "company"        => "Shippo",
            "street1"        => "215 Clayton St.",
            "street2"        => "",
            "city"           => "San Francisco",
            "state"          => "CA",
            "zip"            => "94117",
            "country"        => "US",
            "phone"          => "+1 555 341 9393",
            "email"          => "api@goshippo.com",
            "is_residential" => true,
            "metadata"       => "integration test"
        );
        $addressTo = ShippoClient::provider(self::$accessToken)->addresses()->create($param);
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Addresses\\Address', $addressTo);

        return array(
            'address_from' => $addressFrom->getObjectId(),
            'address_to' => $addressTo->getObjectId(),
        );
    }

    /**
     * @test
     * @depends createAddress
     * @param $objectIds
     */
    public function retrieveAddress($objectIds)
    {
        $response = ShippoClient::provider(self::$accessToken)->addresses()->retrieve($objectIds['address_from']);
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Addresses\\Address', $response);
    }

    /**
     * @test
     * @depends createAddress
     * @param $objectIds
     */
    public function validateAddress($objectIds)
    {
        $response = ShippoClient::provider(self::$accessToken)->addresses()->validate($objectIds['address_from']);
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Addresses\\Address', $response);
    }

    /**
     * @test
     * @depends createAddress
     */
    public function getListOfAddress()
    {
        $response = ShippoClient::provider(self::$accessToken)->addresses()->getList();

        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Addresses\\AddressCollection', $response);
        $responseArray = $response->toArray();
        $this->assertGreaterThanOrEqual(1, $responseArray['count']);
        $this->assertArrayHasKey('next', $responseArray);
        $this->assertArrayHasKey('previous', $responseArray);
        $this->assertContainsOnlyInstancesOf('ShippoClient\\Http\\Response\\Addresses\\Address', $responseArray['results']);
    }

    /**
     * @test
     * @depends createAddress
     * @param $objectIds
     */
    public function createParcel($objectIds)
    {
        $param = array(
            'length'        => 5,
            'width'         => 5,
            'height'        => 5,
            'distance_unit' => 'cm',
            'weight'        => 2,
            'mass_unit'     => 'lb',
            'template'      => '',
            'metadata'      => 'Customer ID 123456',
        );
        $parcel = ShippoClient::provider(self::$accessToken)->parcels()->create($param);

        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Parcels\\Parcel', $parcel);
        $parcelArray = $parcel->toArray();
        $this->assertSame('VALID', $parcelArray['object_state']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/', $parcelArray['object_created']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/', $parcelArray['object_updated']);
        $this->assertNotEmpty($parcelArray['object_id']);
        $this->assertNotEmpty($parcelArray['object_owner']);
        $this->assertSame('', $parcelArray['template']);
        $this->assertSame(5.0, $parcelArray['length']);
        $this->assertSame(5.0, $parcelArray['width']);
        $this->assertSame(5.0, $parcelArray['height']);
        $this->assertSame('cm', $parcelArray['distance_unit']);
        $this->assertSame(2.0, $parcelArray['weight']);
        $this->assertSame('lb', $parcelArray['mass_unit']);
        $this->assertSame('', $parcelArray['value_amount']);
        $this->assertSame('', $parcelArray['value_currency']);
        $this->assertSame('Customer ID 123456', $parcelArray['metadata']);

        $objectIds['parcel'] = $parcel->getObjectId();

        return $objectIds;
    }

    /**
     * @test
     * @depends createParcel
     * @param $objectIds
     */
    public function retrieveParcel($objectIds)
    {
        $response = ShippoClient::provider(self::$accessToken)->parcels()->retrieve($objectIds['parcel']);
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Parcels\\Parcel', $response);
    }

    /**
     * @test
     * @depends createParcel
     */
    public function getListOfParcel()
    {
        $response = ShippoClient::provider(self::$accessToken)->parcels()->getList();
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Parcels\\ParcelCollection', $response);
        $responseArray = $response->toArray();
        $this->assertGreaterThanOrEqual(1, $responseArray['count']);
        $this->assertArrayHasKey('next', $responseArray);
        $this->assertArrayHasKey('previous', $responseArray);
        $this->assertContainsOnlyInstancesOf('ShippoClient\\Http\\Response\\Parcels\\Parcel', $responseArray['results']);
    }

    /**
     * @test
     * @depends createParcel
     * @param $objectIds
     */
    public function createShipment($objectIds)
    {
        $param = array(
            'object_purpose'  => 'PURCHASE',
            'address_from'    => $objectIds['address_from'],
            'address_to'      => $objectIds['address_to'],
            'parcel'          => $objectIds['parcel'],
            'submission_type' => 'PICKUP',
            'submission_date' => date(\DateTime::ISO8601),
        );

        $shipment = ShippoClient::provider(self::$accessToken)->shipments()->create($param);

        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Shipments\\Shipment', $shipment);
        $shipmentArray = $shipment->toArray();
        $this->assertInternalType('array', $shipmentArray['carrier_accounts']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/', $shipmentArray['object_created']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/', $shipmentArray['object_updated']);
        $this->assertNotEmpty($shipmentArray['object_id']);
        $this->assertNotEmpty($shipmentArray['object_owner']);
        $this->assertSame('VALID', $shipmentArray['object_state']);
        $this->assertSame('QUEUED', $shipmentArray['object_status']);
        $this->assertSame('PURCHASE', $shipmentArray['object_purpose']);
        $this->assertSame($objectIds['address_from'], $shipmentArray['address_from']);
        $this->assertSame($objectIds['address_to'], $shipmentArray['address_to']);
        $this->assertSame($objectIds['parcel'], $shipmentArray['parcel']);
        $this->assertSame('PICKUP', $shipmentArray['submission_type']);
        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $shipmentArray['submission_date']);
        $this->assertNotEmpty($shipmentArray['address_return']);
        $this->assertSame('', $shipmentArray['return_of']);
        $this->assertSame('', $shipmentArray['customs_declaration']);
        $this->assertSame(0, $shipmentArray['insurance_amount']);
        $this->assertSame('', $shipmentArray['insurance_currency']);
        $this->assertInternalType('array', $shipmentArray['extra']);
        $this->assertSame('', $shipmentArray['reference_1']);
        $this->assertSame('', $shipmentArray['reference_2']);
        $this->assertNotEmpty($shipmentArray['rates_url']);
        $this->assertInternalType('array', $shipmentArray['messages']);
        $this->assertSame('', $shipmentArray['metadata']);

        $objectIds['shipment'] = $shipment->getObjectId();
        return $objectIds;
    }

    /**
     * @test
     * @depends createShipment
     * @param $objectIds
     */
    public function retrieveShipment($objectIds)
    {
        $response = ShippoClient::provider(self::$accessToken)->shipments()->retrieve($objectIds['shipment']);
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Shipments\\Shipment', $response);
    }

    /**
     * @test
     * @depends createShipment
     */
    public function getListOfShipment()
    {
        $response = ShippoClient::provider(self::$accessToken)->shipments()->getList();
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\Shipments\\ShipmentCollection', $response);
        $responseArray = $response->toArray();
        $this->assertGreaterThanOrEqual(1, $responseArray['count']);
        $this->assertArrayHasKey('next', $responseArray);
        $this->assertArrayHasKey('previous', $responseArray);
        $this->assertContainsOnlyInstancesOf('ShippoClient\\Http\\Response\\Shipments\\Shipment', $responseArray['results']);
    }
}
