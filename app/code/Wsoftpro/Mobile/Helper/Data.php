<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Wsoftpro\Mobile\Helper;
use Magento\Framework\App\Helper\Context;
use Wsoftpro\Mobile\Api\HomepageInterface;

/**
 * Helper Data.
 * @category Magestore
 * @package  Magestore_Storepickup
 * @module   Storepickup
 * @author   Magestore Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements HomepageInterface
{
    protected $_custom;
    protected $_storeManager;
    protected $_helperProducts;
    protected $_helperCustom;
    protected $_collectionFactory;
    protected $_productCollectionFactory;
    protected $context;
    protected $resultJsonFactory;
    public function __construct(

        \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom,
        \WeltPixel\OwlCarouselSlider\Helper\Products $helperProducts,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \WeltPixel\OwlCarouselSlider\Block\Slider\Custom $custom,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\ResourceModel\Page\Grid\CollectionFactory  $collectionFactory,
        array $data = []

    )
    {
        $this->_productCollectionFactory  = $productsCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_helperCustom = $helperCustom;
        $this->_custom = $custom;
        $this->_helperProducts  = $helperProducts;
        $this->_cartHelper = $context->getCartHelper();
        $this->resultJsonFactory = $resultJsonFactory;
    }
    function get_numerics ($str) {

        preg_match_all('/^[0-9]*$/', $str, $matches);
        return $matches;
    }
    public function getSliderData(){
        $contentHomepage = $this->_collectionFactory->create()->addFilter('title','Home Page')->getFirstItem()->getData('content');
        $arrayStringHomepage = explode('</p>',$contentHomepage);
        $sliderId = 'slider_id';
        foreach ($arrayStringHomepage as $value){
            if(strpos($value, $sliderId)){
                $key =  preg_replace('/[^0-9]/', '', $value);
                $data['sliderConfig'] = $this->_helperCustom->getSliderConfigOptions($key)->getData();
                $data['Breakpoint'] = $this->_helperCustom->getBreakpointConfiguration();
                $data['MediaUrl'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
                $data['GatEnable'] = $this->_helperCustom->isGatEnabled();
                $data['MobileBreakPoint'] = $this->_helperCustom->getMobileBreakPoint();
//                $slider[$key] = array(
//                    'typeSlider' => $data['sliderConfig']['slider_config']['title'],
//                    'bannerConfig'  => array(
//                        'title' => array(
//                            'text' => $data['sliderConfig']['banner_config']['title'],
//                            'color' => $data['sliderConfig']['banner_config']['color_title']
//                        ),
//                        'desc' => array(
//                            'text' => $data['sliderConfig']['banner_config']['description'],
//                            'color' => $data['sliderConfig']['banner_config']['color_description']
//                        ),
//                        'notify' => array(
//                            'text' => $data['sliderConfig']['banner_config']['notify'],
//                            'color' => $data['sliderConfig']['banner_config']['color_notify']
//                        ),
//                        'subDesc' => array(
//                            'text' => $data['sliderConfig']['banner_config']['sub_desc'],
//                            'color' => $data['sliderConfig']['banner_config']['color_subdesc']
//                        ),
//                        'action' => array(
//                            'url' => $data['sliderConfig']['banner_config']['url'],
//                            'buttonText' => $data['sliderConfig']['banner_config']['button_text'],
//                            'buttonColor'=> $data['sliderConfig']['banner_config']['button_color'],
//                            'backgroundColor' => $data['sliderConfig']['banner_config']['background_color'],
//                            'borderColor' => $data['sliderConfig']['banner_config']['border_color']
//                        )
//                    )
//
//
//                );
            }
        }
        return $data;
    }

    public function getTypeProduct(){

    }
    public function getProductbestsell()
    {

        $data = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceCollection = $objectManager->create('Wsoftpro\Mobile\Block\Slider\Products');
        $_collection = $this->_productCollectionFactory->create();

        $config_homepage = $this->_collectionFactory->create()->addFilter('title','Home Page')->getFirstItem()->getData();

        $config = $config_homepage['content'];
        if(strpos($config,'"bestsell_products"')){
            $type ="bestsell_products";
            $products = $resourceCollection->useGetBestsellProductCollection($_collection);
            $data['config']= $this->getSliderConfigOptions($type);
            if($products){
                $key = 0;
                foreach ($products as $product){
                    $data['product'][$key]['image'] = $product->getId();
                    $data['product'][$key]['image'] =  $product->getImage();
                    $data['product'][$key]['url']  = $product->getUrlModel()->getUrl($product);
                    $data['product'][$key]['name']  =  $product->getName();
                    $data['product'][$key]['price']  = $product->getPrice();
                    $data['product'][$key]['addtocarturl']  = $this->getAddToCartUrl($product);
                    $data['product'][$key]['addtowishlistparams'] = $this->getAddToWishlistParams($product);
                    $key++;
                }
            }
        }

        return $data;

    }
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    public function GetAddToWishlistParams($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->create('Magento\Catalog\Helper\Product\Compare');
        $data = $resource->getAddToWishlistParams($product);
        return $data;
    }
    public function getSliderConfigOptions($type){
        return  $this->_helperProducts->getSliderConfigOptions($type);
    }


    public function getHomepage()
    {
        return json_encode($this->getSliderData());
    }


}
