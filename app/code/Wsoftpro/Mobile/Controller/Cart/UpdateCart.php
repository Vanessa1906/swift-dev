<?php

namespace Wsoftpro\Mobile\Controller\Cart;
use Wsoftpro\Mobile\Api\UpdateCartInterface;
class UpdateCart extends \Magento\Framework\App\Helper\AbstractHelper implements UpdateCartInterface
{
    protected $_scopeConfig;


    protected $_checkoutSession;

    protected $_storeManager;


    protected $_formKeyValidator;


    protected $_cart;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_cart = $cart;
    }


    public function updateCart()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $cartData = array(
                '39' => array(
                    'qty' => 0
                )
            );
            if (is_array($cartData)) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                if (!$this->_cart->getCustomerSession()->getCustomerId() && $this->_cart->getQuote()->getCustomerId()) {
                    $this->_cart->getQuote()->setCustomerId(null);
                }

                $cartData = $this->_cart->suggestItemsQty($cartData);
                $this->_cart->updateItems($cartData)->save();
            }
        }  catch (\Exception $e) {
            $data = array(
                'status'  => 404,
                'message' => 'Customer Not Found'
            );
        }
        return json_encode($data);
    }

}
