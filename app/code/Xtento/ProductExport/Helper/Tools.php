<?php

/**
 * Product:       Xtento_ProductExport (2.3.9)
 * ID:            cb9PRAWlxmJOwg/jsj5X3dDv0+dPZORkauC/n26ZNAU=
 * Packaged:      2017-10-04T08:29:55+00:00
 * Last Modified: 2017-04-27T20:05:32+00:00
 * File:          app/code/Xtento/ProductExport/Helper/Tools.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Helper;

class Tools extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Xtento\ProductExport\Model\ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var \Xtento\ProductExport\Model\DestinationFactory
     */
    protected $destinationFactory;

    /**
     * Tools constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Xtento\ProductExport\Model\ProfileFactory $profileFactory
     * @param \Xtento\ProductExport\Model\DestinationFactory $destinationFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Xtento\ProductExport\Model\ProfileFactory $profileFactory,
        \Xtento\ProductExport\Model\DestinationFactory $destinationFactory
    ) {
        parent::__construct($context);
        $this->profileFactory = $profileFactory;
        $this->destinationFactory = $destinationFactory;
    }

    /**
     * @param $profileIds
     * @param $destinationIds
     *
     * @return string
     */
    public function exportSettingsAsJson($profileIds, $destinationIds)
    {
        $randIdPrefix = rand(100000, 999999);
        $exportData = [];
        $exportData['profiles'] = [];
        $exportData['destinations'] = [];
        foreach ($profileIds as $profileId) {
            $profile = $this->profileFactory->create()->load($profileId);
            if ($profile->getId()) {
                $profile->unsetData('profile_id');
                $profileDestinationIds = $profile->getData('destination_ids');
                $newDestinationIds = [];
                foreach (explode("&", $profileDestinationIds) as $destinationId) {
                    if (is_numeric($destinationId)) {
                        $newDestinationIds[] = substr($randIdPrefix . $destinationId, 0, 8);
                    }
                }
                $profile->setData('new_destination_ids', implode("&", $newDestinationIds));
                $exportData['profiles'][] = $profile->toArray();
            }
        }
        foreach ($destinationIds as $destinationId) {
            $destination = $this->destinationFactory->create()->load($destinationId);
            if ($destination->getId()) {
                $destination->setData('new_destination_id', substr($randIdPrefix . $destinationId, 0, 8));
                #$destination->unsetData('destination_id');
                $destination->unsetData('password');
                $exportData['destinations'][] = $destination->toArray();
            }
        }
        return \Zend_Json::encode($exportData);
    }

    /**
     * @param $jsonData
     * @param array $addedCounter
     * @param array $updatedCounter
     * @param bool $updateByName
     * @param string $errorMessage
     *
     * @return bool
     */
    public function importSettingsFromJson($jsonData, &$addedCounter = [], &$updatedCounter = [], $updateByName = true, &$errorMessage = "")
    {
        try {
            $settingsArray = \Zend_Json::decode($jsonData);
        } catch (\Exception $e) {
            $errorMessage = __('Import failed. Decoding of JSON import format failed.');
            return false;
        }
        // Remapped destination IDs
        $remappedDestinationIds = [];
        // Process destinations
        if (isset($settingsArray['destinations'])) {
            foreach ($settingsArray['destinations'] as $destinationData) {
                if ($updateByName) {
                    $destinationCollection = $this->destinationFactory->create()->getCollection()
                        ->addFieldToFilter('type', $destinationData['type'])
                        ->addFieldToFilter('name', $destinationData['name']);
                    if ($destinationCollection->getSize() === 1) {
                        $remappedDestinationIds[$destinationData['new_destination_id']] = $destinationCollection->getFirstItem()->getId();
                        unset($destinationData['new_destination_id']);
                        $this->destinationFactory->create()->setData($destinationData)->setId($destinationCollection->getFirstItem()->getId())->save();
                        $updatedCounter['destinations']++;
                    } else {
                        $newDestination = $this->destinationFactory->create()->setData($destinationData);
                        if (isset($destinationData['new_destination_id'])) {
                            $newDestination->setId($destinationData['new_destination_id']);
                            unset($destinationData['new_destination_id']);
                            $newDestination->saveWithId();
                        } else {
                            unset($destinationData['new_destination_id']);
                            $newDestination->save();
                        }
                        $addedCounter['destinations']++;
                    }
                } else {
                    $newDestination = $this->destinationFactory->create()->setData($destinationData);
                    if (isset($destinationData['new_destination_id'])) {
                        $newDestination->setId($destinationData['new_destination_id']);
                        unset($destinationData['new_destination_id']);
                        $newDestination->saveWithId();
                    } else {
                        unset($destinationData['new_destination_id']);
                        $newDestination->save();
                    }
                    $addedCounter['destinations']++;
                }
            }
        }
        // Process profiles
        if (isset($settingsArray['profiles'])) {
            foreach ($settingsArray['profiles'] as $profileData) {
                if ($updateByName) {
                    $profileCollection = $this->profileFactory->create()->getCollection()
                        ->addFieldToFilter('entity', $profileData['entity'])
                        ->addFieldToFilter('name', $profileData['name']);
                    if (isset($profileData['new_destination_ids'])) {
                        $newDestinationIds = explode("&", $profileData['new_destination_ids']);
                        $tempDestinationIds = [];
                        foreach ($newDestinationIds as $newDestinationId) {
                            if (isset($remappedDestinationIds[$newDestinationId])) {
                                $newDestinationId = $remappedDestinationIds[$newDestinationId];
                            }
                            $tempDestinationIds[] = $newDestinationId;
                        }
                        $profileData['destination_ids'] = implode("&", $newDestinationIds);
                        unset($profileData['new_destination_ids']);
                    }
                    if ($profileCollection->getSize() === 1) {
                        $this->profileFactory->create()->setData($profileData)->setId($profileCollection->getFirstItem()->getId())->save();
                        $updatedCounter['profiles']++;
                    } else {
                        $this->profileFactory->create()->setData($profileData)->save();
                        $addedCounter['profiles']++;
                    }
                } else {
                    if (isset($profileData['new_destination_ids'])) {
                        $profileData['destination_ids'] = $profileData['new_destination_ids'];
                        unset($profileData['new_destination_ids']);
                    }
                    $this->profileFactory->create()->setData($profileData)->save();
                    $addedCounter['profiles']++;
                }
            }
        }
        return true;
    }
}
