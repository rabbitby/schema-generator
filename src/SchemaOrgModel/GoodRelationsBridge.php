<?php

/*
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SchemaOrgModel;

/**
 * Schema.org to GoodRelations bridge
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class GoodRelationsBridge
{
    const GOOD_RELATIONS_NAMESPACE = 'http://purl.org/goodrelations/v1#';
    const RDF_SCHEMA_NAMESPACE = 'http://www.w3.org/2000/01/rdf-schema#';

    protected $goodRelations;

    /**
     * @var array
     */
    protected static $goodRelationsObjectPropertiesTable = [
        'priceSpecification' => 'hasPriceSpecification',
        'businessFunction' => 'hasBusinessFunction',
        'eligibleCustomerType' => 'eligibleCustomerTypes',
        'manufacturer' => 'hasManufacturer',
        'warrantyScope' => 'hasWarrantyScope',
        'inventoryLevel' => 'hasInventoryLevel',
        'dayOfWeek' => 'hasOpeningHoursDayOfWeek',
        'brand' => 'hasBrand',
        'itemOffered' => 'includes',
        'makesOffer' => 'offers',
        'availableDeliveryMethod' => 'availableDeliveryMethods',
        'openingHoursSpecification' => 'hasOpeningHoursSpecification',
        'eligibleQuantity' => 'hasEligibleQuantity',
        'warranty' => 'hasWarrantyPromise',
        'acceptedPaymentMethod' => 'acceptedPaymentMethods'
    ];
    /**
     * @var array
     */
    protected static $goodRelationsDatatypePropertiesTable = [
        'minPrice' => 'hasMinCurrencyValue',
        'unitCode' => 'hasUnitOfMeasurement',
        'isicV4' => 'hasISICv4',
        'gtin8' => 'hasGTIN-8',
        'maxPrice' => 'hasMaxCurrencyValue',
        'gtin14' => 'hasGTIN-14',
        'maxValue' => 'hasMaxValue',
        'mpn' => 'hasMPN',
        'value' => 'hasValue',
        'model' => 'hasMakeAndModel',
        'gtin13' => 'hasEAN_UCC-13',
        'globalLocationNumber' => 'hasGlobalLocationNumber',
        'naics' => 'hasNAICS',
        'priceCurrency' => 'hasCurrency',
        'sku' => 'hasStockKeepingUnit',
        'duns' => 'hasDUNS',
        'minValue' => 'hasMinValue',
        'eligibleRegion' => 'eligibleRegions'
    ];

    /**
     * @param \SimpleXMLElement $goodRelations
     */
    public function __construct(\SimpleXMLElement $goodRelations)
    {
        $this->goodRelations = $goodRelations;
        $this->goodRelations->registerXPathNamespace('rdfs', static::RDF_SCHEMA_NAMESPACE);
    }

    /**
     * Checks if a property exists in GoodRelations
     *
     * @param  string $id
     * @return bool
     */
    public function exist($id)
    {
        $result = $this->goodRelations->xpath(sprintf('//*[@rdf:about="%s"]', static::getPropertyUrl($id)));

        return !empty($result);
    }

    /**
     * Extracts cardinality from the Good Relations OWL
     *
     * @param  string      $id
     * @return string|bool
     */
    public function extractCardinality($id)
    {
        $result = $this->goodRelations->xpath(sprintf('//*[@rdf:about="%s"]/rdfs:label', static::getPropertyUrl($id)));
        if (count($result)) {
            preg_match('/\(.\.\..\)/', $result[0]->asXML(), $matches);

            return $matches[0];
        }

        return false;
    }

    /**
     * Converts Schema.org's id to Good Relations id
     *
     * @param  string $id
     * @return string
     */
    private static function convertPropertyId($id)
    {

        if (isset (static::$goodRelationsDatatypePropertiesTable[$id])) {
            return static::$goodRelationsDatatypePropertiesTable[$id];
        }

        if (isset (static::$goodRelationsObjectPropertiesTable[$id])) {
            return static::$goodRelationsObjectPropertiesTable[$id];
        }

        return $id;
    }

    /**
     * Gets a property URL
     *
     * @param  string $id
     * @return string
     */
    private static function getPropertyUrl($id)
    {
        return sprintf('%s%s', static::GOOD_RELATIONS_NAMESPACE, static::convertPropertyId($id));
    }
}
