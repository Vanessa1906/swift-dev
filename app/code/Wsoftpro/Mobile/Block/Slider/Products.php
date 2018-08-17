<?php
namespace Wsoftpro\Mobile\Block\Slider;

class Products extends \WeltPixel\OwlCarouselSlider\Block\Slider\Products
{

    public function useGetBestsellProductCollection($collection){
        return $this->_getBestsellProductCollection($collection);
    }

}
