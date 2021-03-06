<?php
namespace Icube\DecimalUpdate\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Magento\Catalog\Model\Product\Visibility;

class RunUpdateCommand extends Command
{
	/**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     */
    const XML_PATH_DATA = 'icube_section/execute/point_customer_attribute_id';
 
    protected function configure()
    {
        $this->setName('icube:run_update')->setDescription('Run update command.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $connection = $resource->getConnection();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $tablename = $scopeConfig->getValue(self::XML_PATH_DATA, $storeScope);

        $sql = "SELECT CONCAT( 'ALTER TABLE ', TABLE_NAME, ' MODIFY COLUMN ', COLUMN_NAME, ' TIMESTAMP DEFAULT CURRENT_TIMESTAMP' ) AS TABLE_NAME FROM information_schema.columns WHERE table_schema = '".$tablename."' AND column_default = '0000-00-00 00:00:00'";
        $result = $connection->fetchAll($sql);

        $sql1 = "SELECT CONCAT( 'ALTER TABLE ', TABLE_NAME, ' MODIFY COLUMN ', COLUMN_NAME, ' decimal(17,4)' ) AS TABLE_NAME FROM information_schema.columns WHERE table_schema = '".$tablename."' AND column_type = 'decimal(12,4)'";
        $result1 = $connection->fetchAll($sql1); 

        foreach ($result as $key => $value) {
            $result2 = $connection->query($value['TABLE_NAME']);

        }

        foreach ($result1 as $key1 => $value1) {
             $result3 = $connection->query($value1['TABLE_NAME']);
         } 

         if($result && $result1) {
            $response['status'] = 'success';
         } else {
            $response['status'] = 'query already run';
         }

        $output->writeln('Run update database finish.');
    }
 
}