<?php

namespace Flat3\OData\Tests\Unit\Protocol;

use Flat3\OData\Tests\Data\FlightModel;
use Flat3\OData\Tests\Request;
use Flat3\OData\Tests\TestCase;

class ServiceMetadataTest extends TestCase
{
    use FlightModel;

    public function test_has_empty_metadata_document_at_document_root()
    {
        $this->assertXmlResponse(
            Request::factory()
                ->xml()
                ->path('/$metadata')
        );
    }

    public function test_has_flight_metadata_document_at_document_root()
    {
        $this->withFlightModel();

        $this->assertXmlResponse(
            Request::factory()
                ->xml()
                ->path('/$metadata')
        );
    }
}
