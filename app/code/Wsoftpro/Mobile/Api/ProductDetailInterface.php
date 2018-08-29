<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 8/27/2018
 * Time: 3:58 PM
 */

namespace Wsoftpro\Mobile\Api;


interface ProductDetailInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param int $productId product id.
     * @return array|int|string|bool|float Scalar or array of scalars.
     */
    public function getProductDetail($productId);

}