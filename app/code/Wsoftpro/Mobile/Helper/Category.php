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
use Wsoftpro\Mobile\Api\CategoriesInterface;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;

class Category extends \Magento\Framework\App\Helper\AbstractHelper implements CategoriesInterface
{
    protected $_storeManager;
    protected $_collectionFactory;
    protected $_categoryFactory;
    protected $_blockFactory;
    protected $_helperCustom;
    protected $_MobileData;
    protected $_products;
    protected $_priceHelper;
    protected $_wishlistCollectionFactory;
    public function __construct(
        \Wsoftpro\Mobile\Helper\Data $MobileData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\ResourceModel\Page\Grid\CollectionFactory  $collectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom,
        \Magento\Catalog\Model\Product $products,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        CollectionFactory $wishlistCollectionFactory


    )
    {
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_blockFactory = $blockFactory;
        $this->_helperCustom = $helperCustom;
        $this->_MobileData = $MobileData;
        $this->_products = $products;
        $this->_priceHelper = $priceHelper;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;

    }

    public function getFooterBlock(){
        $data = array();
        try{
            $footerContent = $this->_collectionFactory->create()->addFilter('identifier','footer-mobile')->getFirstItem()->getData();
            if($footerContent){
                $data['title_banner'] = $footerContent['content_heading'];
                $arrayStringHomepage = explode('</p>',$footerContent['content']);
                foreach ($arrayStringHomepage as $block){
                    $block = str_replace('<p>','',$block);
                    if($block){
                        $data['banner'][] = $this->getImageHtmlDeclaration($block);
                    }
                }
                $data['title'] = $footerContent['meta_title'];
                $data['description'] = $footerContent['meta_description'];
            }else{
                $data = array(
                    'status' => '404',
                    'message' => 'Not found'
                );
            }

            return json_encode($data);
        }catch (\Exception $e){
            $e->getMessage();
        }

    }
    public function getImageHtmlDeclaration($filename, $renderAsTag = false)
    {
        $wysiwyg = "wysiwyg/";
        $string = strstr($filename,$wysiwyg);
        $imageJpg = strpos($string, '"}}' );
        if($imageJpg) {
            $result = substr($string, 0, $imageJpg);
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$result;
    }
    public function getCategory()
    {
        $categoryId = $this->getCategoryId();
        $category = $this->_categoryFactory->create()->load($categoryId);
        return $category;
    }
    public function getProductCollection()
    {
        return $this->getCategory()->getProductCollection()->addAttributeToSelect('*');
    }

    /**
     * @param int $id category id
     * @param int $page page number
     * @param int $customerId customer id
     * @return string
     */
    public function getProductByCategory($id,$page,$customerId){
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $pageSize = 4;
        $productsCategory = array();
        $productsCategory['data'] = array();
        $data = array();
        try{

            $category = $this->_categoryFactory->create()->load($id);
            $totalProduct = $this->_categoryFactory->create()->load($id)->getProductCollection()->addAttributeToSelect('*');
               $totalProduct->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                $totalProduct->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $totalProduct = count($totalProduct->getData());
            $maxPageSize = round($totalProduct / 4);
            $collection = $this->_categoryFactory->create()->load($id)->getProductCollection()->setPageSize($pageSize)->setCurPage($page)->addAttributeToSelect('*');
            $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

            $wishListCollection;
            if ($customerId != -1) {
                  $wishListCollection = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
             }

            if(count($collection->getData()) > 0){
                foreach ($collection as $productId){
                    if($maxPageSize >= $page){
                        $product = $this->_products->load($productId->getId());
                        $price = $product->getData('price');
                        $formattedPrice = $this->_priceHelper->currency($price, true, false);

                        $data['id'] = (int)$product->getId();
                        $data['name']  =  $product->getName();
                        $data['title'] =  strip_tags($product->getShortDescription());
                        $data['image'] =  $product->getImage();
                        $data['url']  = $product->getUrlModel()->getUrl($product);
                        $data['price']  = number_format($price, 2, '.', '');
                        $data['showing_price']  = $formattedPrice;
                        $data['currency']  = $currency;
                        $data['addtocarturl']  = $this->_MobileData->getAddToCartUrl($product);
                        $data['addtowishlistparams'] = $this->_MobileData->getAddToWishlistParams($product);
                        $data['wish'] = 0;

                        if(isset($wishListCollection)) {
                            $wish = clone $wishListCollection;
                            $wish->addFieldToFilter('product_id', $data['id']);
                             if($wish->count() > 0) {
                                 $data['wish'] = 1;
                             }
                        }

                        array_push($productsCategory['data'],$data);

                        $data = null;
                    }
                }

                $data['mainImageURL'] = array();
                if($page == 1 && $category->getImageUrl()){
                    $data['mainImageURL'] = $category->getImageUrl();
                    $productsCategory['mainImageURL'] = $data['mainImageURL'];
                }

            }else{
                $productsCategory = array(
                    'status' => '404',
                    'message' => 'Not found'
                );
            }

            return json_encode($productsCategory);
        }catch (\Exception $e){
               echo  $e->getMessage();
        }

    }

    public function getSliderByCategoryId($id){
        try{
            $dataArr = array();
            $category = $this->_categoryFactory->create()->load($id)->getData();

            if(isset($category['landing_page'])){
                $sliderCategory = $category['landing_page'];
                $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
                $storeId = $this->_storeManager->getStore()->getId();
                $block = $this->_blockFactory->create()->load($sliderCategory)->getData();
                $value = $block['content'];
                $sliderId = 'slider_id';
                if(strpos($value, $sliderId)){
                    $idSlider =  preg_replace('/[^0-9]/', '', $value);
                    $data['sliderConfig'] = $this->_helperCustom->getSliderConfigOptions($idSlider)->getData();
                    $slider = array();
                    $slider['data'] = array();
                    $slider['type'] = (int)$data['sliderConfig']['slider_config']['type_slider'];
                    $banners = $data['sliderConfig']['banner_config'];
                    $item = array();
                    foreach ($banners as $key => $banner){
                        $item = array(
                            'type' => (int)$banner['type_banner'],
                            'imageURL' => $mediaUrl.$banner['image'],
                            'title' => array(
                                'text' => $banner['title'],
                                'color' => $this->_MobileData->rgb2HEXhtml($banner['color_title'])
                            ),
                            'desc' => array(
                                'text' => $banner['description'],
                                'color' => $this->_MobileData->rgb2HEXhtml($banner['color_description']),
                            ),
                            'notify' => array(
                                'text' => $banner['notify'],
                                'color' => $this->_MobileData->rgb2HEXhtml($banner['color_notify'])
                            ),
                            'subDesc' => array(
                                'text' => $banner['subdesc'],
                                'color' => $this->_MobileData->rgb2HEXhtml($banner['color_subdesc'])
                            ),
                            'action' => array(
                                'url' => $banner['url'],
                                'buttonText' => $banner['button_text'],
                                'buttonColor'=> $this->_MobileData->rgb2HEXhtml($banner['button_color']),
                                'backgroundColor' => $this->_MobileData->rgb2HEXhtml($banner['background_color']),
                                'borderColor' => $this->_MobileData->rgb2HEXhtml($banner['border_color'])
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




                }

            }elsE{
                $dataArr = array(
                    'status' => '404',
                    'message' => 'slider not found'
                );
            }
            return json_encode($dataArr);
        }catch (\Exception $e) {
            $dataArr = array(
                'status' => 'error',
                'message' => 'slider not found'
            );
            return $dataArr;
            $e->getMessage();
        }

    }

}
