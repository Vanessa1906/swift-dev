<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 8/24/2018
 * Time: 4:44 PM
 */

namespace Wsoftpro\Mobile\Model;

use Wsoftpro\Mobile\Api\WishListInterface;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class WishList implements WishListInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * @var $_wishlistRepository
     */
    protected $_wishlistRepository;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var Item
     */
    protected $_itemFactory;

    /**
     * @var WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * WishList constructor.
     * @param CollectionFactory $wishlistCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     */

    public function __construct(
        CollectionFactory $wishlistCollectionFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        WishlistFactory $wishlistFactory
    )
    {
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_wishlistRepository= $wishlistRepository;
        $this->_productRepository = $productRepository;
        $this->_itemFactory = $itemFactory;
        $this->_wishlistFactory = $wishlistFactory;
    }

    public function getWishListForCustomer($customerId){
        if (empty($customerId) || !isset($customerId) || $customerId == "") {
            throw new InputException(__('Id required'));
        } else {
            $collection =
                $this->_wishlistCollectionFactory->create()
                    ->addCustomerIdFilter($customerId);

            $wishlistData = [];
            foreach ($collection as $item) {
                $productInfo = $item->getProduct()->toArray();
                $data = [
                    "wishlist_item_id" => $item->getWishlistItemId(),
                    "wishlist_id"      => $item->getWishlistId(),
                    "product_id"       => $item->getProductId(),
                    "store_id"         => $item->getStoreId(),
                    "added_at"         => $item->getAddedAt(),
                    "description"      => $item->getDescription(),
                    "qty"              => round($item->getQty()),
                    "product"          => $productInfo
                ];
                $wishlistData[] = $data;
            }
            return json_encode($wishlistData);
        }
    }

    public function addWishListForCustomer($customerId, $productId){
        if ($productId == null) {
            throw new LocalizedException(__
            ('Invalid product, Please select a valid product'));
        }
        try {
            $product = $this->_productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }
        try {
            $wishlist = $this->_wishlistRepository->create()->loadByCustomerId
            ($customerId, true);
            $wishlist->addNewItem($product);
            $returnData = $wishlist->save();
        } catch (NoSuchEntityException $e) {

        }
        $data['code'] = 0;
        $data['message'] = 'successfully';
        return json_encode($data);

    }

    public function deleteWishListForCustomer($customerId, $productId){
        if ($productId == null) {
            throw new LocalizedException(__
            ('Invalid product, Please select a valid product'));
        }
        try {
            $product = $this->_productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }
        try {
            $wishlist = $this->_wishlistRepository->create()->loadByCustomerId
            ($customerId, true);
            $wishlist->delete($product);
            $returnData = $wishlist->save();
        } catch (NoSuchEntityException $e) {

        }
        $data['code'] = 0;
        $data['message'] = 'successfully';
        return json_encode($data);
    }
}