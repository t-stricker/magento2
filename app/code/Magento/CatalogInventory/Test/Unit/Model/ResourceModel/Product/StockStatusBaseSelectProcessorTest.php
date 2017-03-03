<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogInventory\Test\Unit\Model\ResourceModel\Product;

use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Magento\CatalogInventory\Model\ResourceModel\Product\StockStatusBaseSelectProcessor;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Indexer\Model\ResourceModel\FrontendResource;

class StockStatusBaseSelectProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var FrontendResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $indexerStockFrontendResource;

    /**
     * @var StockStatusBaseSelectProcessor
     */
    private $stockStatusBaseSelectProcessor;

    protected function setUp()
    {
        $this->resource = $this->getMockBuilder(ResourceConnection::class)->disableOriginalConstructor()->getMock();
        $this->select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $this->indexerStockFrontendResource = $this->getMockBuilder(FrontendResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockStatusBaseSelectProcessor =  (new ObjectManager($this))->getObject(
            StockStatusBaseSelectProcessor::class,
            [
                'resource' => $this->resource,
                'indexerStockFrontendResource' => $this->indexerStockFrontendResource
            ]
        );
    }

    public function testProcess()
    {
        $tableName = 'table_name';

        $this->indexerStockFrontendResource->expects($this->once())
            ->method('getMainTable')
            ->willReturn($tableName);

        $this->select->expects($this->once())
            ->method('join')
            ->with(
                ['stock' => $tableName],
                sprintf('stock.product_id = %s.entity_id', BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS),
                []
            )
            ->willReturnSelf();
        $this->select->expects($this->once())
            ->method('where')
            ->with('stock.stock_status = ?', Stock::STOCK_IN_STOCK)
            ->willReturnSelf();

        $this->stockStatusBaseSelectProcessor->process($this->select);
    }
}
