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
        array $data = [],
        array $slider = [],
        array $result = []

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
    public function getHomeData(){
        $dataArr = array(); //biggest container.
        $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $contentHomepage = $this->_collectionFactory->create()->addFilter('title','Home Page')->getFirstItem()->getData('content');
        $arrayStringHomepage = explode('</p>',$contentHomepage);
        $sliderId = 'slider_id';
        $productType = "products_type";
        foreach ($arrayStringHomepage as $ids => $value){
            if(strpos($value, $sliderId)){

                $idSlider =  preg_replace('/[^0-9]/', '', $value);
                $data['sliderConfig'][$ids] = $this->_helperCustom->getSliderConfigOptions($idSlider)->getData();
                $slider = array();
                $slider['data'] = array();
                $slider['type'] = (int)$data['sliderConfig'][$ids]['slider_config']['type_slider'];
                $banners = $data['sliderConfig'][$ids]['banner_config'];
                $item = array();
                    foreach ($banners as $key => $banner){
                        $item = array(
                            'type' => (int)$banner['type_banner'],
                            'imageURL' => $mediaUrl.$banner['image'],
                            'title' => array(
                                'text' => $banner['title'],
                                'color' => $this->rgb2HEXhtml($banner['color_title'])
                            ),
                            'desc' => array(
                                'text' => $banner['description'],
                                'color' => $this->rgb2HEXhtml($banner['color_description']),
                            ),
                            'notify' => array(
                                'text' => $banner['notify'],
                                'color' => $this->rgb2HEXhtml($banner['color_notify'])
                            ),
                            'subDesc' => array(
                                'text' => $banner['subdesc'],
                                'color' => $this->rgb2HEXhtml($banner['color_subdesc'])
                            ),
                            'action' => array(
                                'url' => $banner['url'],
                                'buttonText' => $banner['button_text'],
                                'buttonColor'=> $this->rgb2HEXhtml($banner['button_color']),
                                'backgroundColor' => $this->rgb2HEXhtml($banner['background_color']),
                                'borderColor' => $this->rgb2HEXhtml($banner['border_color'])
                            )

                        );
                        array_push($slider['data'],$item);
                        if($slider['type'] == 20){
                            array_push($slider['data'],$item);
                            break;
                        }




                    }

                array_push($dataArr,$slider);
                $slider = null;




            }elseif(strpos($value, $productType)){
                $productArr = $this->getProducts($value);
                array_push($dataArr,$productArr);
            }



        }
        return $dataArr;
    }

    function rgb2HEXhtml($rgba)
    {
        $rgbaArray = explode(',',$rgba);
        $opacity = 1;
        if(isset($rgbaArray[3])){
            $opacity = $rgbaArray[3];
        }

        $r = implode(',',$rgbaArray);
         $g=-1;
         $b=-1;
        if (is_array($r) && sizeof($r) == 3)
            list($r, $g, $b) = $r;
        $r = intval($r); $g = intval($g);
        $b = intval($b);
        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));
        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;
        $color ='#'.$color. "." .$opacity;
        return $color;
    }
//you should pass r,g,b value and call function

    public function getProducts($value)
    {

        $data = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceCollection = $objectManager->create('Wsoftpro\Mobile\Block\Slider\Products');
        $_collection = $this->_productCollectionFactory->create()->addAttributeToSelect('title_rewrite');
        $data['type'] = "30";
        $data['title'] = array();
        if(strpos($value,'"bestsell_products"')){
            $type ="bestsell_products";
            $products = $resourceCollection->useGetBestsellProductCollection($_collection);
        }elseif(strpos($value,'"new_products"')){
            $type ="new_products";
            $products = $resourceCollection->useGetNewProductCollection($_collection);
        }elseif(strpos($value,'"sell_products"')){
            $type ="sell_products";
            $products = $resourceCollection->useGetSellProductCollection($_collection);
        }elseif(strpos($value,'"recently_viewed"')){
            $type ="recently_viewed";
            $products = $resourceCollection->useGetRecentlyViewedCollection($_collection);
        }elseif(strpos($value,'"related_products"')){
            $type ="related_products";
            $products = $resourceCollection->getProductCollectionRelated();
        }elseif(strpos($value,'"upsell_products"')){
            $type ="upsell_products";
            $products = $resourceCollection->getProductCollectionUpSell();
        }elseif(strpos($value,'"crosssell_products"')){
            $type ="crosssell_products";
            $products = $resourceCollection->getProductCollectionCrossSell();
        }
        $config = $this->getSliderConfigOptions($type);
        $data['title'] = $config['title'];
        $data['title'] = $config['title'];
        if($products && is_array($products) && count($products) > 1){
            $key = 0;
            foreach($products as $product){
                $data['products'][$key]['id'] = $product->getId();
                $data['products'][$key]['title'] =  $product->getTitleRewrite();
                $data['products'][$key]['image'] =  $product->getImage();
                $data['products'][$key]['url']  = $product->getUrlModel()->getUrl($product);
                $data['products'][$key]['name']  =  $product->getName();
                $data['products'][$key]['price']  = $product->getPrice();
                $data['products'][$key]['addtocarturl']  = $this->getAddToCartUrl($product);
                $data['products'][$key]['addtowishlistparams'] = $this->getAddToWishlistParams($product);
                $key++;
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
        // Use Return instead of echo for getting response as JSON String.
        // In Request, Accept and Content-Type must set to 'application/json'
        return json_encode($this->getHomeData(), true);
    }


}
