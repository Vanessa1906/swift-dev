<?php
/**
 * Created by PhpStorm.
 * User: Phung Thuc
 * Date: 8/13/2018
 * Time: 11:15 PM
 */

namespace Wsoftpro\Mobile\Api;


interface CustomerSessionInterface
{
    /**
     * @api
     * @param string $user username.
     * @param string $pass password.
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    public function CustomerLogin($user,$pass);

    /**
     * @api
     * @param int $customerId customer id.
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    public function getCustomerCartItem($customerId);

}