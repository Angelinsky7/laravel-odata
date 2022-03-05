<?php

declare(strict_types=1);

namespace Flat3\Lodata\Tests\Laravel\Models;

use Flat3\Lodata\Attributes\LodataBoolean;
use Flat3\Lodata\Attributes\LodataByte;
use Flat3\Lodata\Attributes\LodataCollection;
use Flat3\Lodata\Attributes\LodataDate;
use Flat3\Lodata\Attributes\LodataDateTimeOffset;
use Flat3\Lodata\Attributes\LodataDecimal;
use Flat3\Lodata\Attributes\LodataDouble;
use Flat3\Lodata\Attributes\LodataDuration;
use Flat3\Lodata\Attributes\LodataEnum;
use Flat3\Lodata\Attributes\LodataGuid;
use Flat3\Lodata\Attributes\LodataIdentifier;
use Flat3\Lodata\Attributes\LodataInt16;
use Flat3\Lodata\Attributes\LodataInt32;
use Flat3\Lodata\Attributes\LodataInt64;
use Flat3\Lodata\Attributes\LodataSByte;
use Flat3\Lodata\Attributes\LodataSingle;
use Flat3\Lodata\Attributes\LodataTimeOfDay;
use Flat3\Lodata\Attributes\LodataTypeIdentifier;
use Flat3\Lodata\Attributes\LodataUInt16;
use Flat3\Lodata\Attributes\LodataUInt32;
use Flat3\Lodata\Attributes\LodataUInt64;
use Flat3\Lodata\Tests\Laravel\Models\Enums\Colour;
use Flat3\Lodata\Tests\Laravel\Models\Enums\MultiColour;
use Flat3\Lodata\Type\SByte;
use Illuminate\Database\Eloquent\Model;

#[
    LodataIdentifier('Alternative'),
    LodataTypeIdentifier('AlternativeType'),
    LodataGuid(name: 'Id', key: true),
    LodataBoolean(name: 'One', source: 'one'),
    LodataByte(name: 'Two'),
    LodataCollection(name: 'Three'),
    LodataCollection(name: 'ThreeOne', underlyingType: SByte::class),
    LodataCollection(name: 'ThreeTwo', underlyingType: 'Recs'),
    LodataCollection(name: 'ThreeThree', underlyingType: 'Colours'),
    LodataDate(name: 'Four'),
    LodataDateTimeOffset(name: 'Five'),
    LodataDecimal(name: 'Six', nullable: false),
    LodataDouble(name: 'Seven'),
    LodataDuration(name: 'Eight', nullable: false),
    LodataEnum(name: 'Nine', enum: 'Colours'),
    LodataEnum(name: 'NineOne', enum: 'Colours', isFlags: false),
    LodataEnum(name: 'NineTwo', enum: 'MultiColours', isFlags: true),
    LodataInt16(name: 'Ten'),
    LodataInt32(name: 'Eleven'),
    LodataInt64(name: 'Twelve'),
    LodataSByte(name: 'Thirteen'),
    LodataSingle(name: 'Fourteen'),
    LodataTimeOfDay(name: 'Fifteen'),
    LodataUInt16(name: 'Sixteen'),
    LodataUInt32(name: 'Seventeen'),
    LodataUInt64(name: 'Eighteen')
]
class AllAttribute extends Model
{

}