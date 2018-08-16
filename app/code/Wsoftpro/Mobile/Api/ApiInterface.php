<?php
/**
 * Created by PhpStorm.
 * User: Phung Thuc
 * Date: 8/13/2018
 * Time: 11:15 PM
 */

namespace Wsoftpro\Mobile\Api;


interface  ApiInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function getHomepage();
}