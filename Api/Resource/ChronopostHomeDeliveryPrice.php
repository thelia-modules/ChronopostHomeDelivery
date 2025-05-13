<?php

namespace ChronopostHomeDelivery\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ChronopostHomeDelivery\Model\Map\ChronopostHomeDeliveryPriceTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Bridge\Propel\State\PropelCollectionProvider;
use Thelia\Api\Bridge\Propel\State\PropelItemProvider;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: 'admin/chronopost/home-delivery/prices',
            paginationEnabled: true,
            provider: PropelCollectionProvider::class,
        ),
        new Get(
            uriTemplate: 'admin/chronopost/home-delivery/prices/{id}',
            provider: PropelItemProvider::class,
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_READ_ADMIN]],
)]

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: 'front/chronopost/home-delivery/prices',
            paginationEnabled: true,
            provider: PropelCollectionProvider::class,
        ),
        new Get(
            uriTemplate: 'front/chronopost/home-delivery/prices/{id}',
            provider: PropelItemProvider::class,
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_READ_FRONT]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'areaId' => 'exact',
        'deliveryModeId' => 'exact',
    ]
)]
class ChronopostHomeDeliveryPrice implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_READ_ADMIN = 'admin:chronopost_price:read';
    public const GROUP_READ_FRONT = 'front:chronopost_price:read';

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?int $id = null;

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?int $areaId = null;

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?int $deliveryModeId = null;

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?float $weightMax = null;

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?float $priceMax = null;

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?float $francoMinPrice = null;

    #[Groups([self::GROUP_READ_ADMIN])]
    private ?float $price = null;

    #[Ignore]
    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return ChronopostHomeDeliveryPriceTableMap::getTableMap();
    }
}
