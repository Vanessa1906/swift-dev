<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel;

/**
 * Class ProductVariantPerformance
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
class ProductVariantPerformance extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_variant_performance', 'id');
    }
}
