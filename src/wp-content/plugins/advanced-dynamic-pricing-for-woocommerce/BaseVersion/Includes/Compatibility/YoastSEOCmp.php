<?php

namespace ADP\BaseVersion\Includes\Compatibility;

defined('ABSPATH') or exit;

/**
 * Plugin Name: Yoast SEO
 * Author: Team Yoast
 *
 * @see https://yoast.com/#utm_term=team-yoast&utm_content=plugin-info
 */
class YoastSEOCmp
{
    static function isNewPriceSpecification() {
        return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '9.5.0' ) >= 0;
    }

    public function applyCompatibility()
    {
        add_action("adp_schema_data_ready", function($data, $processedProduct, $decimals){

            add_filter( 'wpseo_schema_product', function($wpseo_data) use ($data) {
                if (isset($wpseo_data['hasVariant']) || !isset($wpseo_data['offers'])) {
                    return $wpseo_data;
                }

                if (isset($wpseo_data['offers'][0]['priceSpecification'][0])) {
                    $priceSpecification = $wpseo_data['offers'][0]['priceSpecification'][0];
                } else {
                    $priceSpecification = $wpseo_data['offers'][0]['priceSpecification'];
                }

                if ( isset( $priceSpecification['price']) && isset($data['price']) ) {
                    $priceSpecification['price'] = $data['price'];

                    if (YoastSEOCmp::isNewPriceSpecification()) {
                        $wpseo_data['offers'][0]['priceSpecification'] = [ $priceSpecification ];
                    } else {
                        $wpseo_data['offers'][0]['priceSpecification'] = $priceSpecification;
                    }
                }

                return $wpseo_data;
            });

            add_filter('wpseo_schema_offer', function($offer) use ($processedProduct, $decimals) {
                $childPrices = YoastSEOCmp::getChildPrices($processedProduct, $decimals);
                if(isset($childPrices)) {
                    if (isset($offer['priceSpecification'][0])) {
                        $priceSpecification = $offer['priceSpecification'][0];
                    } else {
                        $priceSpecification = $offer['priceSpecification'];
                    }

                    foreach($childPrices as $child) {
                        if (isset($offer['priceSpecification'][0])) {
                            $offer['priceSpecification'] = [ $priceSpecification ];
                        } else {
                            $offer['priceSpecification'] = $priceSpecification;
                        }
                        $priceSpecification = $offer['priceSpecification'];

                        if(
                            isset($child['priceOriginal'])
                            && isset($priceSpecification['price'])
                            && isset($child['price'])
                            && $child['priceOriginal'] === $priceSpecification['price']
                        ) {
                            $priceSpecification['price'] = $child['price'];

                            if (YoastSEOCmp::isNewPriceSpecification()) {
                                $offer['priceSpecification'] = [ $priceSpecification ];
                            } else {
                                $offer['priceSpecification'] = $priceSpecification;
                            }
                        }
                    }
                }

                return $offer;
            });
        }, 10, 3);
    }

    /**
     * @param $processedProduct
     * @param $decimals
     *
     * @return array
     */
    private static function getChildPrices($processedProduct, $decimals) {
        $childPrices = array();
        foreach ($processedProduct->getChildren() as $child) {
            $price = $child->getPrice();
            $priceOriginal = $child->getOriginalPrice();
            $childPrices[] = ['price' => wc_format_decimal($price, $decimals), 'priceOriginal' => wc_format_decimal($priceOriginal, $decimals)];
        }
        return $childPrices;
    }

    public function isActive()
    {
        return defined('WPSEO_WOO_VERSION') && defined('WPSEO_BASENAME');
    }
}
