<?php

namespace Wsoftpro\Mobile\Controller\Cart;
use Wsoftpro\Mobile\Api\AddToCartInterface;
class Addtocart extends \Magento\Framework\App\Helper\AbstractHelper implements AddToCartInterface
{
    protected $storeManager;
    protected $quoteModel;
    protected $_customerRepositoryInterface;
    protected $productRepository;

    public function __construct(
        /* Add below dependencies */
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Quote\Model\Quote $quoteModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->storeManager                 = $storeManager;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteModel                   = $quoteModel;
        $this->productRepository            = $productRepository;
    }

    public function checkoutCart($id,$customerId){

        try {
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $quote    = $this->quoteModel->loadByCustomer($customer);
            if (!$quote->getId()) {
                $quote->setCustomer($customer);
                $quote->setIsActive(1);
                $quote->setStoreId($this->storeManager->getStore()->getId());
            }


            $product = $this->productRepository->getById($id);
            $quote->addProduct($product, $customerId);
            $quote->collectTotals()->save();
            $data = array(
                'name' => 'toan',
                'old'   => 23
            );
            return $data;

        } catch (\Exception $e) {
            //echo $e->getMessage(); die;
        }
    }
}
