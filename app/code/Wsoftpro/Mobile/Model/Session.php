<?php

namespace Wsoftpro\Mobile\Model;
use Wsoftpro\Mobile\Api\SessionInterface;
class Session extends \Magento\Framework\App\Helper\AbstractHelper implements SessionInterface
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
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteModel,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->_checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->quoteModel=$quoteModel;
        $this->quoteRepository = $quoteRepository;
    }
    public function CustomerLogin($user,$pass)
    {
        try{
            $data = array();
            $data['type'] = 'custommer';
            $customer = $this->customerAccountManagement->authenticate($user, $pass);
            if($customer){
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->session->regenerateId();
                $data = $this->session->getCustomer()->getData();
                $data['message'] = "Login success";
            }else{

                $data['message'] = "Invalid login or password.";
            }
        }catch (\Exception $e){
            $e->getMessage();
        }


        return $data;

    }
    public function getCustomerCartItem($customerId){
        $customer = $this->quoteRepository->getForCustomer($customerId);
        $items = $customer->getAllItems();
        foreach ($items as $item){
            $data[] = $item->getProductId();
        }
        return json_encode($data);
    }
}
