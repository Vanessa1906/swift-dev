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
    protected $_category;
    protected $_categoryRepository;
    protected $session;
    protected $_cart;
    protected $_categoryFactory;
    protected $_priceHelper;
    public function __construct(
        \Magento\Catalog\Helper\Category $category,
        \Magento\Checkout\Model\Session $session,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom,
        \WeltPixel\OwlCarouselSlider\Helper\Products $helperProducts,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \WeltPixel\OwlCarouselSlider\Block\Slider\Custom $custom,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\ResourceModel\Page\Grid\CollectionFactory  $collectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
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
        $this->_category = $category;
        $this->_categoryRepository = $categoryRepository;
        $this->_session = $session;
        $this->_cart = $cart;
        $this->_categoryFactory = $categoryFactory;
        $this->_priceHelper = $priceHelper;

    }

    public function getSubCategory()
    {
        $data = array();
        $subCategory = array();
        $categories = $this->_category->getStoreCategories();
        foreach($categories as $category) {
            $model = $this->_categoryFactory->create()->load($category->getId());
            $subCategory = array(
                'id' => (int)$category->getId(),
                'name' => $category->getName(),
                'request_path' => $category->getRequestPath().$category->getRequestPath(),
                'childCategories' => $this->getSubChildCategory($category->getId())

            );
            array_push($data,$subCategory);


        }
        return $data;
    }
    public function getSubChildCategory($id){
        $sub = array();
        $subCategory = array();

            $categoryObj = $this->_categoryRepository->get($id);
            $subcategories = $categoryObj->getChildrenCategories();
            foreach($subcategories as $subcategorie) {
                $subCategory = array(
                    'id' => (int)$subcategorie->getId(),
                    'name' => $subcategorie->getName(),
                    'request_path' => $subcategorie->getRequestPath().$subcategorie->getRequestPath(),
                    'childCategories' => $this->getSubChildCategory($subcategorie->getId())
                );
                array_push($sub,$subCategory);
            }

        return $sub;

    }
    /**
     *  get data Home page
     *  return array
     * */
    public function getHomeData(){
        $dataArr = array(); //biggest container.
        $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        /*get home page content in static block title Home page*/
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

    /**
     * @param $rgba
     * @return string
     *
     */
    function rgb2HEXhtml($rgba)
    {

        $rgbaArray = explode(',',$rgba);
        $opacity = 1;
        //if something unpredictable, fallback to white with hex code of FFF and alpha = 1
        $color = 'fff';
        //we need at least 3 values for RGB
        if(count($rgbaArray) >= 3) {
            //get opacity value if avaiable, otherwise set it to one
            if(isset($rgbaArray[3]) &&
                is_numeric($rgbaArray[3])){
                $opacity = $rgbaArray[3];
            }
            //make sure 3 values is valid
            if(is_numeric($rgbaArray[0]) &&
                is_numeric($rgbaArray[1]) &&
                is_numeric($rgbaArray[2])
            ) {
                $color = sprintf("%x", ($rgbaArray[0] << 16) + ($rgbaArray[1] << 8) + $rgbaArray[2]);
            }
        }
        $color =$color. "," .$opacity;

        return $color;
    }
    /**
     * @param $value
     * get product by type $value
     * @return array
     *
     */
    public function getProducts($value)
    {

        $data = [];
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceCollection = $objectManager->create('Wsoftpro\Mobile\Block\Slider\Products');
        $_collection = $this->_productCollectionFactory->create()->addAttributeToSelect('title_rewrite');
        $data['type'] = 30;
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
        $data['products'] = array();
        if($products && is_array($products->getData()) && count($products->getData()) > 1){
            $key = 0;
            foreach($products as $product){
                $formattedPrice = $this->_priceHelper->currency($product->getPrice(), true, false);
                $data['products'][$key]['id'] = (int)$product->getId();
                $data['products'][$key]['title'] =  strip_tags($product->getShortDescription());
                $data['products'][$key]['image'] =  $product->getImage();
                $data['products'][$key]['url']  = $product->getUrlModel()->getUrl($product);
                $data['products'][$key]['name']  =  $product->getName();
                $data['products'][$key]['price']  = number_format($product->getPrice(), 2, '.', '');
                $data['products'][$key]['showing_price']  = $formattedPrice;
                $data['products'][$key]['currency']  = $currency;
                $data['products'][$key]['addtocarturl']  = $this->getAddToCartUrl($product);
                $data['products'][$key]['addtowishlistparams'] = $this->getAddToWishlistParams($product);
                $key++;
            }

        }

        return $data;

    }

    /**
     * @param $product
     * @param array $additional
     * @return string
     *
     */
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
    /**
     * $param $type
     * get slider config in admin page
     * return array
     *
     * */
    public function getSliderConfigOptions($type){
        return  $this->_helperProducts->getSliderConfigOptions($type);
    }
    public  function getItemcart(){
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $items=  $this->_cart->getQuote()->getAllItems();
        $data = [];
        $subTotal = $this->_session->getQuote()->getSubtotal();
        $grandTotal = $this->_session->getQuote()->getGrandTotal();
        if($subTotal && $grandTotal){
            $data['subTotal']=$subTotal;
            $data['grandTotal']=$grandTotal;
        }

        $key=0;
        foreach($items as $item) {
            if($item->getProductType() != "configurable"){
                $formattedPrice = $this->_priceHelper->currency($item->getPrice(), true, false);
                $data['item'][$key]['id']= (int)$item->getProductId();
                $data['item'][$key]['type']=$item->getProductType();
                $data['item'][$key]['name']=$item->getName();
                $data['item'][$key]['img']=$item->getImage();
                $data['item'][$key]['sku']=$item->getSku();
                $data['item'][$key]['qty']=$item->getQty();
                $data['item'][$key]['price']=$item->getPrice();
                $data['item'][$key]['price']  = number_format($item->getPrice(), 2, '.', '');
                $data['item'][$key]['showing_price']  = $formattedPrice;
                $data['item'][$key]['currency']  = $currency;
                $key++;
            }

        }
        return $data;
    }
    /**
     * get Homepage data
     * return type json
     *
     * */
    public function getHomepage()
    {
        // Use Return instead of echo for getting response as JSON String.
        // In Request, Accept and Content-Type must set to 'application/json'
        return json_encode($this->getHomeData(), true);
    }

    /**
     * @return string
     *
     *
     */
    public function getHomeMenu()
    {
        // Use Return instead of echo for getting response as JSON String.
        // In Request, Accept and Content-Type must set to 'application/json'
        return json_encode($this->getSubCategory(), true);
    }

    /**
     * @return string
     */
    public function getCart()
    {
        // Use Return instead of echo for getting response as JSON String.
        // In Request, Accept and Content-Type must set to 'application/json'
        return json_encode($this->getItemcart(), true);
    }

}
