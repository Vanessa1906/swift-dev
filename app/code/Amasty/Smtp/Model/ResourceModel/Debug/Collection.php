<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Model\ResourceModel\Debug;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Smtp\Model\Debug', 'Amasty\Smtp\Model\ResourceModel\Debug');
    }
}
