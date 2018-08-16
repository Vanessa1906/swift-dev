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
use Wsoftpro\Mobile\Api\ApiInterface;

/**
 * Helper Data.
 * @category Magestore
 * @package  Magestore_Storepickup
 * @module   Storepickup
 * @author   Magestore Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements ApiInterface
{
    protected $_custom;
    protected $_storeManager;
    protected $_helperCustom;
    protected $_collectionFactory;
    protected $_productCollectionFactory;
    public function __construct(
        \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory,
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
                $data[$key]['sliderConfig'] = $this->_helperCustom->getSliderConfigOptions($key)->getData();
                $data[$key]['Breakpoint'] = $this->_helperCustom->getBreakpointConfiguration();
                $data[$key]['MediaUrl'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
                $data[$key]['GatEnable'] = $this->_helperCustom->isGatEnabled();
                $data[$key]['MobileBreakPoint'] = $this->_helperCustom->getMobileBreakPoint();
            }
        }
        return $data;
    }
    public function getHomepage()
    {
        return json_encode($this->getSliderData());
    }


}
