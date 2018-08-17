<?php
namespace Wsoftpro\Mobile\Block\Adminhtml\Banner\Edit\Tab;

class Banner extends \WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Edit\Tab\Banner
{
    const BANNER_ONE  = 101;
    const BANNER_TOW  = 102;

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $dataObj = $this->_objectFactory->create();

        $form = $this->getForm();
        $bannerModel = $this->_coreRegistry->registry('banner');
        if ($sliderId = $this->getRequest()->getParam('loaded_slider_id')) {
            $bannerModel->setSliderId($sliderId);
        }

        $form->setHtmlIdPrefix($this->_bannerModel->getFormFieldHtmlIdPrefix());
        $fieldset = $form->getElement('base_fieldset');
        if ($bannerModel->getId()) {
            $fieldset->removeField('id');
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
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
            'text',
            [
                'name'     => 'border_color',
                'label'    => __('Color Border Button'),
                'title'    => __('Color Border Button'),
            ]
        );
        $fieldset->addField(
            'type_banner',
            'select',
            [
                'name'     => 'type_banner',
                'label'    => __('Type Banner'),
                'title'    => __('Type Banner'),
                'options' => $this->getTypeBanner(),
            ]
        );
        $form->addValues($bannerModel->getData());
        $this->setForm($form);
        return \Magento\Backend\Block\Widget\Form::_prepareForm();
    }
    public function getTypeBanner()
    {
        return [
            self::BANNER_ONE  => __('Banner 1'),
            self::BANNER_TOW  => __('Banner 2'),
        ];
    }

}