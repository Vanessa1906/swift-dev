<?php
namespace Wsoftpro\Mobile\Block\Adminhtml\Slider\Edit\Tab;

class Form extends \WeltPixel\OwlCarouselSlider\Block\Adminhtml\Slider\Edit\Tab\Form
{


    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            'type_slider',
            'text',
            [
                'name'     => 'type_slider',
                'label'    => __('Title Color'),
                'title'    => __('Title Color')
            ]
        );
        return \Magento\Backend\Block\Widget\Form::_prepareForm();
    }

}