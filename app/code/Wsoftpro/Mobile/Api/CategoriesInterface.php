<?php
/**
 * Created by PhpStorm.
 * User: Phung Thuc
 * Date: 8/13/2018
 * Time: 11:15 PM
 */

namespace Wsoftpro\Mobile\Api;


interface CategoriesInterface
{
    /**
     * @return mixed
     *
     */
   public function getFooterBlock();

    /**
     * @api
     * @param int $id Category id.
     * @return array|int|string|bool|float Scalar or array of scalars
     */
   public function getSliderByCategoryId($id);

    /**
     * @api
     * @param int $id Category id.
     * @param int $page page id.
     * @param int $customerId customer id.
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    public function getProductByCategory($id,$page,$customerId);

}