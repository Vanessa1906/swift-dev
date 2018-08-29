<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 8/24/2018
 * Time: 11:15 PM
 */

namespace Wsoftpro\Mobile\Api;


interface  WishListInterface
{

    /**
     * Returns greeting message to user
     *
     * @api
     * @param int $customerId Customer Id.
     * @return int Greeting message with Customer Id
     */

    public function getWishListForCustomer($customerId);

    /**
     * Returns greeting message to user
     *
     * @api
     * @param int $customerId Customer Id.
     * @param int $productId Product Id.
     * @return int Greeting message with Customer Id, Product id
     */

    public function addWishListForCustomer($customerId, $productId);

    /**
     * Returns greeting message to user
     *
     * @api
     * @param int $customerId Customer Id.
     * @param int $productId Product Id .
     * @return int Greeting message with Customer Id, Product Id
     */

    public function deleteWishListForCustomer($customerId, $productId);

}