<?php
/**
 * Created by PhpStorm.
 * User: Phung Thuc
 * Date: 8/13/2018
 * Time: 11:15 PM
 */

namespace Wsoftpro\Mobile\Api;


interface  HomepageInterface
{
    /**
     * @return mixed
     *
     */
    public function getHomepage();

    /**
     * @return mixed
     *
     */
    public function getHomeMenu();

    /**
     * @return mixed
     *
     */
    public function getCart();

}