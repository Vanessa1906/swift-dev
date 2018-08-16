<?php
namespace Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Category
 * @package Aheadworks\Layerednav\Model\ResourceModel\Layer\Filter
 */
class Category extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('catalog_category_product_index', 'entity_id');
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function joinFilterToCollection(FilterInterface $filter)
    {
        $collection = $filter->getLayer()->getProductCollection();
        if (!$collection->getFlag('category_table_joined')) {
            $collection
                ->getSelect()
                ->join(
                    ['cat' => $this->getMainTable()],
                    'cat.product_id = e.entity_id'
                )
            ;
            $collection->setFlag('category_table_joined', true);
        }

        return $this;
    }

    /**
     * Get where conditions
     *
     * @param array $value
     * @return array
     */
    public function getWhereConditions($value)
    {
        $value = implode("','", $value);
        return ["cat.category_id IN ('{$value}')"];
    }

    /**
     * Get products count in category
     *
     * @param FilterInterface $filter
     * @param \Magento\Catalog\Model\Category $category
     * @return int
     */
    public function getProductCount(FilterInterface $filter, $category)
    {
        $filterSelect = $this->getFilterSelectForProductCount($filter);

        $productTable = $this->getMainTable();
        $select = $this->getConnection()->select()->from(
            ['main_table' => $productTable],
            [new \Zend_Db_Expr('COUNT(DISTINCT main_table.product_id)')]
        )->joinInner(
            $filterSelect,
            'main_table.product_id = t.entity_id',
            ''
        )->where(
            'main_table.category_id = :category_id'
        );

        $bind = ['category_id' => (int)$category->getId()];
        $counts = $this->getConnection()->fetchOne($select, $bind);

        return intval($counts);
    }

    /**
     * Get filter select prepared for using in product counting
     *
     * @param FilterInterface $filter
     * @return Select
     */
    private function getFilterSelectForProductCount(FilterInterface $filter)
    {
        $filterSelect = clone $filter->getLayer()->getProductCollection()->getSelect();

        $whereSelectPart = $filterSelect->getPart(Select::WHERE);
        $columnWasUnsetFlag = false;
        foreach ($whereSelectPart as $key => $column) {
            if ((strpos($column, 'cat_index') !== false) || (strpos($column, 'cat') !== false)) {
                unset($whereSelectPart[$key]);
                $columnWasUnsetFlag = true;
            }
        }

        $whereSelectPart = array_values($whereSelectPart);

        if (
            $columnWasUnsetFlag
            && count($whereSelectPart)
            && (stripos($whereSelectPart[0], Select::SQL_AND) !== false)
        ) {
            $andPosition = stripos($whereSelectPart[0], Select::SQL_AND);
            if ($andPosition == 0) {
                $whereSelectPart[0] = substr_replace($whereSelectPart[0], '', $andPosition, strlen(Select::SQL_AND));
            }
        }

        $filterSelect->setPart(Select::WHERE, $whereSelectPart);

        $filterSelect->reset(Select::COLUMNS)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
        ;

        $filterSelect->columns('e.entity_id');

        return $filterSelect;
    }
}
