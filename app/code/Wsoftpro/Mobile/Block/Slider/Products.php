<?php
namespace Wsoftpro\Mobile\Block\Slider;

class Products extends \WeltPixel\OwlCarouselSlider\Block\Slider\Products
{

    public function useGetBestsellProductCollection($collection){
        return $this->_getBestsellProductCollection($collection);
    }
    public function useGetNewProductCollection($collection){
        return $this->_getNewProductCollection($collection);
    }
    public function useGetSellProductCollection($collection){
        return $this->_getSellProductCollection($collection);
    }
    public function useGetRecentlyViewedCollection($collection){
        return $this->_getRecentlyViewedCollection($collection);
    }

}
