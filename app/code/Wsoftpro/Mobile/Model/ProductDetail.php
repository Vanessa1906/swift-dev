<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 8/27/2018
 * Time: 4:01 PM
 */

namespace Wsoftpro\Mobile\Model;

use Wsoftpro\Mobile\Api\ProductDetailInterface;


class ProductDetail implements ProductDetailInterface
{
    /**
     * @var $_storeManager
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
    }

    public function getProductDetail($productId)
    {
        $dataProduct = [];
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $products = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);

        if($products->getTypeId() == 'configurable'){
            $_children = $products->getTypeInstance()->getUsedProducts($products);
            foreach ($_children as $child){
                $color = $child->getColor();
                $size = $child->getSize();
                $showingPrice = $child->getPrice();
                $attributeColor = $products->getResource()->getAttribute('color');
                $attributeSize = $products->getResource()->getAttribute('size');
                $connection  = $objectManager->create('\Magento\Framework\App\ResourceConnection');
                $colorSwaft = $connection->getConnection()->fetchRow('SELECT value FROM eav_attribute_option_swatch where option_id='.$color);
                foreach ($colorSwaft as $_colorSwaft){
                    $_colorSwafts = trim($_colorSwaft, '#');
                }
                $formattedPrice = $priceHelper->currency($showingPrice, true, false);
                if ($attributeColor->usesSource()&& $attributeSize->usesSource()){
                    $sizearr = [
                        'id' => $size,
                        'label' => $attributeSize->getSource()->getOptionText($size),
                    ];
                    $colorarr = [
                        'color_code' => $_colorSwafts,
                        'label' => $attributeColor->getSource()->getOptionText($color)
                    ];
                    $data = [
                        'id_child' => $child->getId(),
                        'sku' => $child->getSku(),
                        'color' => $colorarr,
                        'size' => $sizearr,
                        'showing_price' => $formattedPrice,
                        'price' => $showingPrice,
                        'quantity' => $child->getQty(),
                        'image' => $child->getImage()
                    ];
                }
                $dataProduct[] = $data;
            }
        }

        $dataProduct['id'] = $products->getId();
        $dataProduct['name'] = $products->getName();
        $dataProduct['short_description'] = '' ;
        $dataProduct['store'] = array();
        $dataProduct['main_image'] = $products->getImage();
        $dataProduct['description'] = $products->getDescription();
        $dataProduct['currency'] = $currency;

        return json_encode($dataProduct);
    }
}