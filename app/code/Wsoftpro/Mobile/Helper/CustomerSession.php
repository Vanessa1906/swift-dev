<?php

namespace Wsoftpro\Mobile\Helper;

use Wsoftpro\Mobile\Api\CustomerSessionInterface;

class CustomerSession extends \Magento\Framework\App\Helper\AbstractHelper implements CustomerSessionInterface
{
    protected $customerAccountManagement;

    /** @var Validator */
    protected $formKeyValidator;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var Session
     */
    protected $session;


    protected $_checkoutSession;

    protected $quoteFactory;

    protected $quoteModel;

    protected $quoteRepository;

    protected $_products;
    protected $_cart;
    protected $_storeManager;
    protected $_priceHelper;
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteModel,
        \Magento\Catalog\Model\Product $products,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        $this->session = $customerSession;
        $this->_storeManager = $storeManager;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->_checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->quoteModel = $quoteModel;
        $this->quoteRepository = $quoteRepository;
        $this->_products = $products;
        $this->_cart = $cart;
        $this->_priceHelper = $priceHelper;

    }

    public function CustomerLogin($user, $pass)
    {
        try {
            $data = array();
            $data['type'] = 'custommer';
            $customer = $this->customerAccountManagement->authenticate($user, $pass);
            if ($customer) {
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->session->regenerateId();
                $data = $this->session->getCustomer()->getData();
                $data['message'] = "Login success";
            } else {

                $data['message'] = "Invalid login or password.";
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }


        return json_encode($data, true);

    }

    public function getCustomerCartItem($customerId)
    {
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $data = array();
        $dataItem = array();
        $data['data'] = array();
        try{
            $customer = $this->quoteRepository->getForCustomer($customerId);
            $items = $customer->getAllItems();
            foreach ($items as $item) {
                if ($item->getProductType() != 'configurable') {
                    $product = $this->_products->load($item->getProductId());
                    $color = $product->getResource()->getAttribute('color')->getFrontend()->getValue($product);
                    $size = $product->getResource()->getAttribute('size')->getFrontend()->getValue($product);
                    $images = $product->getMediaGalleryImages();
                    foreach ($images as $child) {
                        $imageUrl = $child->getUrl();
                    }


                    $qty = $item->getQty();
                    $total_price = $item->getRowTotal();
                    $itemId = $item->getItemId();
                    if($item->getParentItem()){
                        $parentItem = $item->getParentItem()->getData();
                        $qty = $parentItem['qty'];
                        $total_price = $parentItem['base_row_total_incl_tax'];
                        $itemId = $parentItem['item_id'];
                    }
                    $dataItem['product_id'] = $product->getId();
                    $dataItem['item_id'] = $itemId;
                    $dataItem['name'] = $product->getName();
                    $dataItem['qty'] = $qty;
                    $dataItem['short_description'] = $item->getShortDesciption();
                    $dataItem['size'] = $size;
                    $dataItem['color'] = $color;
                    $dataItem['price'] = number_format($product->getPrice(), 2, '.', '');
                    $dataItem['total_price'] = number_format($total_price, 2, '.', '');
                    $dataItem['showing_price']  = $this->_priceHelper->currency($product->getPrice(), true, false);
                    $dataItem['showing_total_price']  = $this->_priceHelper->currency($total_price, true, false);
                    $dataItem['currency']  = $currency;
                    $dataItem['image'] = $imageUrl;
                    array_push($data['data'], $dataItem);
                }

            }
            $data['subtotal'] = $customer->getData('subtotal');
            $data['subtotal_with_discount'] = $customer->getData('subtotal_with_discount');
            $data['couponCode'] = $customer->getCouponCode();
            $data['rowItem'] = $customer->getItemsCount();
        }catch (\Exception $e){
            $data = array(
                'status'  => 404,
                'message' => 'Customer Not Found'
            );
        }

        return json_encode($data);
    }
}
