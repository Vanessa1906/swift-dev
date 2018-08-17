<?php
namespace Wsoftpro\Mobile\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'color_title',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Title Color'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'color_description',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Description Color'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'color_notify',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Color Notify'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'notify',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Notify'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'color_subdesc',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Color Sub Description'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'subdesc',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Sub Description'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'button_color',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Color for Button'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'background_color',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Color Background'
                ]);
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_banners'),
                'border_color',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Color Border Button'
                ]);
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $setup->getConnection()->addColumn($setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'type_slider',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Type Slider'
                ]);
        }
        $setup->endSetup();
    }
}