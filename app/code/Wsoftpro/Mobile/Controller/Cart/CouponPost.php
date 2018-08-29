<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wsoftpro\Mobile\Controller\Cart;
use Wsoftpro\Mobile\Api\CouponCodeInterface;
class CouponPost extends \Magento\Framework\App\Helper\AbstractHelper implements CouponCodeInterface
{

    protected $_quoteRepository;

    protected $_couponFactory;

    protected $_scopeConfig;

    protected $_checkoutSession;

    protected $_storeManager;

    protected $_formKeyValidator;

    protected $_cart;

    protected $_context;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_couponFactory = $couponFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_cart = $cart;
        $this->_context = $context;
    }


    public function addCouponPost($couponCode)
    {
        $data = array();

        $cartQuote = $this->_cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            $data = array(
              'status'  => 404,
                'message' => 'Coupon Code Not Found'
            );

        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->_quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                if (!$itemsCount) {
                    if ($isCodeLengthValid) {
                        $coupon = $this->_couponFactory->create();
                        $coupon->load($couponCode, 'code');
                        if ($coupon->getId()) {
                            $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                            $data = array(
                                'status'  => 200,
                                'message' => 'You used coupon code ' . $couponCode
                            );
                        } else {
                            $data = array(
                                'status'  => 400,
                                'message' => 'The coupon code '. $couponCode .' is not valid.'
                            );
                        }
                    } else {
                        $data = array(
                            'status'  => 400,
                            'message' => 'The coupon code '. $couponCode .' is not valid.'
                        );
                    }
                } else {
                    if ($isCodeLengthValid && $couponCode == $cartQuote->getCouponCode()) {
                        $data = array(
                            'status'  => 200,
                            'message' => 'You used coupon code '. $couponCode
                        );
                    } else {
                        $data = array(
                            'status'  => 400,
                            'message' => 'The coupon code '. $couponCode .' is not valid.'
                        );
                        $this->_cart->save();
                    }
                }
            } else {
                $data = array(
                    'status'  => 200,
                    'message' => 'You canceled the coupon code.'
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $e->getMessage();
        } catch (\Exception $e) {
            $data = array(
                'status'  => 400,
                'message' => 'We cannot apply the coupon code.'
            );
        }

        return $data;
    }

    public function removeCouponPost($customerId)
    {
        $data = array();
        try{
            $customer = $this->_quoteRepository->getForCustomer($customerId);
            $customer->getShippingAddress()->setCollectShippingRates(true);
            $customer->setCouponCode('')->collectTotals();
            $this->_quoteRepository->save($customer);
            $data = array(
                'status'  => 200,
                'message' => 'You canceled the coupon code.'
            );
        }catch (\Exception $e) {
            $data = array(
                'status'  => 404,
                'message' => 'Customer Not Found'
            );
        }

        return $data;
    }
}
