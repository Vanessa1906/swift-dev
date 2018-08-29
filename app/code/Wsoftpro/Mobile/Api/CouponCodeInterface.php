<?php
/**
 * Created by PhpStorm.
 * User: Phung Thuc
 * Date: 8/13/2018
 * Time: 11:15 PM
 */

namespace Wsoftpro\Mobile\Api;


interface CouponCodeInterface
{


    /**
     * @api
     * @param string $couponCode code coupon.
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    public function addCouponPost($couponCode);

    /**
     * @api
     * @param int $customerId customer id.
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    public function removeCouponPost($customerId);


}
