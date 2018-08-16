<?php

namespace Wsoftpro\Mobile\Block\Color\Input;
use Magento\Framework\Escaper;
class Color extends \Magento\Framework\Data\Form\Element\Text
{
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('color');
    }

    /**
     * Get the Html for the element.
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $htmlId = $this->getHtmlId();

        $beforeElementHtml = $this->getBeforeElementHtml();
        if ($beforeElementHtml) {
            $html .= '<label class="addbefore" for="' . $htmlId . '">' . $beforeElementHtml . '</label>';
        }

        $html .= '<input id="' . $htmlId . '" name="' . $this->getName() . '" ' . $this->_getUiId() . ' value="' .
            $this->getEscapedValue() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' style="width: 50px" />';

        $afterElementJs = $this->getAfterElementJs();
        if ($afterElementJs) {
            $html .= $afterElementJs;
        }

        $afterElementHtml = $this->getAfterElementHtml();
        if ($afterElementHtml) {
            $html .= '<label class="addafter" for="' . $htmlId . '">' . $afterElementHtml . '</label>';
        }

        return $html;
    }

}