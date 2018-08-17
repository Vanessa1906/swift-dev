<?php
namespace Wsoftpro\Mobile\Block\Adminhtml\Slider\Edit\Tab;


class Form extends \WeltPixel\OwlCarouselSlider\Block\Adminhtml\Slider\Edit\Tab\Form
{
    protected $_typeslider;
    protected $_fieldFactory;
    const BANNER  = 20;
    const SLIDER  = 10;


    /**
     * [$_bannersliderHelper description].
     *
     * @var \WeltPixel\OwlCarouselSlider\Helper\Data
     */
    protected $_bannersliderHelper;

    /**
     * available status.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\Status
     */
    private $_status;

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $slider = $this->getSlider();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        if ($slider->getId()) {
            $fieldset->removeField('id');
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'type_slider',
            'select',
            [
                'name'     => 'type_slider',
                'label'    => __('Type Slider'),
                'title'    => __('Title Color'),
                'options'  => $this->getTypeSlider()
            ]
        );
        $form->setValues($slider->getData());
        $form->addFieldNameSuffix('slider');
        $this->setForm($form);
        return \Magento\Backend\Block\Widget\Form::_prepareForm();
    }

    /**
     * Retrieve available statuses.
     *
     * @return []
     */
    public function getTypeSlider()
    {
        return [
            self::SLIDER  => __('Type Slider'),
            self::BANNER  => __('Type Banner'),
        ];
    }

}