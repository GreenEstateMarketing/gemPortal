<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static GooglePlaceTypeEnum HOSPITAL()
 * @method static GooglePlaceTypeEnum SCHOOL()
 * @method static GooglePlaceTypeEnum SUPERMARKET()
 * @method static GooglePlaceTypeEnum PHARMACY()
 * @method static GooglePlaceTypeEnum AIRPORT()
 * @method static GooglePlaceTypeEnum BUS_STATION()
 * @method static GooglePlaceTypeEnum SUBWAY_STATION()
 * @method static GooglePlaceTypeEnum TRAIN_STATION()
 * @method static GooglePlaceTypeEnum SHOPPING_MALL()
 * @method static GooglePlaceTypeEnum BANK()
 * @method static GooglePlaceTypeEnum ATM()
 * @method static GooglePlaceTypeEnum RESTAURANT()
 * @method static GooglePlaceTypeEnum CAFE()
 * @method static GooglePlaceTypeEnum PARK()
 * @method static GooglePlaceTypeEnum GYM()
 * @method static GooglePlaceTypeEnum PARKING()
 * @method static GooglePlaceTypeEnum PLACE_OF_WORSHIP()
 * @method static GooglePlaceTypeEnum POLICE()
 * @method static GooglePlaceTypeEnum FIRE_STATION()
 * @method static GooglePlaceTypeEnum GAS_STATION()
 */
class GooglePlaceTypeEnum extends Enum
{
    public const HOSPITAL = 'hospital';
    public const SCHOOL = 'school';
    public const SUPERMARKET = 'supermarket';
    public const PHARMACY = 'pharmacy';
    public const AIRPORT = 'airport';
    public const BUS_STATION = 'bus_station';
    public const SUBWAY_STATION = 'subway_station';
    public const TRAIN_STATION = 'train_station';
    public const SHOPPING_MALL = 'shopping_mall';
    public const BANK = 'bank';
    public const ATM = 'atm';
    public const RESTAURANT = 'restaurant';
    public const CAFE = 'cafe';
    public const PARK = 'park';
    public const GYM = 'gym';
    public const PARKING = 'parking';
    public const PLACE_OF_WORSHIP = 'place_of_worship';
    public const POLICE = 'police';
    public const FIRE_STATION = 'fire_station';
    public const GAS_STATION = 'gas_station';

    /**
     * @var string
     */
    public static $langPath = 'plugins/real-estate::facility.google_place_types';
}
