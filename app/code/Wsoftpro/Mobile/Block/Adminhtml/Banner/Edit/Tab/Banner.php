<?php
namespace Wsoftpro\Mobile\Block\Adminhtml\Banner\Edit\Tab;

class Banner extends \WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Edit\Tab\Banner
{

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            'color_title',
            'text',
            [
                'name'     => 'color_title',
                'label'    => __('Title Color'),
                'title'    => __('Title Color'),
            ]
        );

        $fieldset->addField(
            'color_description',
            'text',
            [
                'name'     => 'color_description',
                'label'    => __('Description Color'),
                'title'    => __('Description Color'),
            ]
        );
        $fieldset->addField(
            'color_notify',
            'text',
            [
                'name'     => 'color_notify',
                'label'    => __('Color Notify'),
                'title'    => __('Color Notify'),
            ]
        );
        $fieldset->addField(
            'notify',
            'text',
            [
                'name'     => 'notify',
                'label'    => __('Notify'),
                'title'    => __('Notify'),
            ]
        );
        $fieldset->addField(
            'color_subdesc',
            'text',
            [
                'name'     => 'color_subdesc',
                'label'    => __('Color Sub Description'),
                'title'    => __('Color Sub Description'),
            ]
        );
        $fieldset->addField(
            'subdesc',
            'text',
            [
                'name'     => 'subdesc',
                'label'    => __('Sub Description'),
                'title'    => __('Sub Description'),
            ]
        );
        $fieldset->addField(
            'button_color',
            'text',
            [
                'name'     => 'button_color',
                'label'    => __('Color for Button'),
                'title'    => __('Color for Button'),
            ]
        );
        $fieldset->addField(
            'background_color',
            'text',
            [
                'name'     => 'background_color',
                'label'    => __('Color Background'),
                'title'    => __('Color Background'),
            ]
        );
        $fieldset->addField(
            'border_color',
            'Wsoftpro\Mobile\Block\Color\Input\Color',
            [
                'name'     => 'border_color',
                'label'    => __('Color Border Button'),
                'title'    => __('Color Border Button'),
            ]
        );
        return \Magento\Backend\Block\Widget\Form::_prepareForm();
    }

}