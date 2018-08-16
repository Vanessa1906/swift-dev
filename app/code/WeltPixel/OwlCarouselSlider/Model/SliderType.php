<?php

namespace WeltPixel\OwlCarouselSlider\Model;

/**
 * Status
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class SliderType
{
    const SLIDER_ONE  = 1;
    const SLIDER_TWO = 2;

    /**
     * Retrieve available statuses.
     *
     * @return []
     */
    public function getSliderType()
    {
        return [
            self::SLIDER_ONE  => __('Slider One'),
            self::SLIDER_TWO => __('Slider Two'),
        ];
    }
}
