<?php

namespace ChronopostHomeDelivery\Config;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Model\ConfigQuery;

class ChronopostHomeDeliveryConst
{
    /** Delivery types Name => Code */
    const CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES = [
        "Chrono13"      => "1",
        "Chrono18"      => "16",
        "ChronoExpress" => "17",
        "ChronoClassic" => "44",
        "Fresh13"       => "2R",
        "Chrono10"     => "2"
    ];
    /** @TODO Add other delivery types */

    /** Chronopost shipper identifiers */
    const CHRONOPOST_HOME_DELIVERY_CODE_CLIENT                    = "chronopost_home_delivery_code";
    const CHRONOPOST_HOME_DELIVERY_PASSWORD                       = "chronopost_home_delivery_password";

    /** WSDL for the Chronopost Shipping Service */
    const CHRONOPOST_HOME_DELIVERY_SHIPPING_SERVICE_WSDL              = "https://ws.chronopost.fr/shipping-cxf/ShippingServiceWS?wsdl";
    //const CHRONOPOST_HOME_DELIVERY_RELAY_SEARCH_SERVICE_WSDL          = "https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS?wsdl";
    const CHRONOPOST_HOME_DELIVERY_COORDINATES_SERVICE_WSDL           = "https://ws.chronopost.fr/rdv-cxf/services/CreneauServiceWS?wsdl";
    /** @TODO Add other WSDL config key */

    /** @Unused */
    const CHRONOPOST_HOME_DELIVERY_TRACKING_URL                   = "https://ws.chronopost.fr/tracking-cxf/TrackingServiceWS/trackSkybillV2";


    /** @Unused */
    public function getTrackingURL()
    {
        $URL = self::CHRONOPOST_HOME_DELIVERY_TRACKING_URL;
        $URL .= "language=" . "fr_FR"; //todo Make locale a variable
        $URL .= "&skybillNumber=" . "XXX"; //todo Use real skybill Number -> getTrackingURL(variable)

        return $URL;
    }

    /** Local static config value, used to limit the number of calls to the DB  */
    protected static $config = null;

    /**
     * Set the local static config value
     */
    public static function setConfig()
    {
        $config = [
            /** Chronopost basic informations */
            self::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT                => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT),
            self::CHRONOPOST_HOME_DELIVERY_PASSWORD                   => ChronopostHomeDelivery::getConfigValue(self::CHRONOPOST_HOME_DELIVERY_PASSWORD),

            /** END */
        ];

        /** Delivery types */
        foreach (self::getDeliveryTypesStatusKeys() as $statusKey) {
            $config[$statusKey] = ChronopostHomeDelivery::getConfigValue($statusKey);
        }

        /** Set the local static config value */
        self::$config = $config;
    }

    /**
     * Return the local static config value or the value of a given parameter
     *
     * @param null $parameter
     * @return array|mixed|null
     */
    public static function getConfig($parameter = null)
    {
        /** Check if the local config value is set, and set it if it's not */
        if (null === self::$config) {
            self::setConfig();
        }

        /** Return the value of the config parameter given, or null if it wasn't set */
        if (null !== $parameter) {
            return (isset(self::$config[$parameter])) ? self::$config[$parameter] : null;
        }

        /** Return the local static config value */
        return self::$config;
    }

    /** Status keys of the delivery types.
     *  @return array
     */
    public static function getDeliveryTypesStatusKeys()
    {
        $statusKeys = [];

        foreach (self::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES as $name => $code) {
            $statusKeys[$name] = 'chronopost_home_delivery_delivery_' . strtolower($name) . '_status';
        }

        return $statusKeys;
    }
}