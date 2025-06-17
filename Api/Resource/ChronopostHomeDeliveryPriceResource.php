<?php

namespace ChronopostHomeDelivery\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Propel\Runtime\Map\TableMap;
use ChronopostHomeDelivery\Model\Map\ChronopostHomeDeliveryPriceTableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/chronopost-home-delivery-price/{id}',
            name: 'api_chronopost_home_delivery_price_get_front'
        ),
        new GetCollection(
            uriTemplate: '/front/chronopost-home-delivery-prices',
            name: 'api_chronopost_home_delivery_price_get_collection_front'
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]]
)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/chronopost-home-delivery-price/{id}',
            name: 'api_chronopost_home_delivery_price_get_admin'
        ),
        new GetCollection(
            uriTemplate: '/admin/chronopost-home-delivery-prices',
            name: 'api_chronopost_home_delivery_price_get_collection_admin'
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]]
)]

class ChronopostHomeDeliveryPriceResource implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_ADMIN_READ = 'admin:chronopost_home_delivery_price:read';
    public const GROUP_FRONT_READ = 'front:chronopost_home_delivery_price:read';

    /**
     * @var int|null
     */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?int $id = null;

    /**
     * @var float|null
     */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?float $weightMax = null;

    /**
     * @var float|null
     */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?float $priceMax = null;

    /**
     * @var float|null
     */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?float $price = null;

    /**
     * @var float|null
     */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?float $francoMinPrice = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return float|null
     */
    public function getWeightMax(): ?float
    {
        return $this->weightMax;
    }

    /**
     * @param float|null $weightMax
     * @return void
     */
    public function setWeightMax(?float $weightMax): void
    {
        $this->weightMax = $weightMax;
    }

    /**
     * @return float|null
     */
    public function getPriceMax(): ?float
    {
        return $this->priceMax;
    }

    /**
     * @param float|null $priceMax
     * @return void
     */
    public function setPriceMax(?float $priceMax): void
    {
        $this->priceMax = $priceMax;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return void
     */
    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return float|null
     */
    public function getFrancoMinPrice(): ?float
    {
        return $this->francoMinPrice;
    }

    /**
     * @param float|null $francoMinPrice
     * @return void
     */
    public function setFrancoMinPrice(?float $francoMinPrice): void
    {
        $this->francoMinPrice = $francoMinPrice;
    }

    /**
     * @return TableMap|null
     */
    #[Ignore]
    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new ChronopostHomeDeliveryPriceTableMap();
    }

}
