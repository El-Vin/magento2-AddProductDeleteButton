<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace DevTest\DevAdmin\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

/**
 * Class AddAttribute
 */
class DeleteButton extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if($this->getProduct()->getId() && !$this->getProduct()->isReadonly()){
            $deleteConfirmMsg = __("Are you sure to delete this product?");
            return [
                'label' => __('Delete'),
                'class' => 'delete primary',
                // 'on_click' => 'location.href = \'' . $this->getDeleteProductUrl() . '\'',
                'on_click' => 'deleteConfirm("' . $deleteConfirmMsg . '", "' . $this->getDeleteProductUrl() . '")',
                'sort_order' => 20
            ];
        }else{
            return [];
        }
    }

    protected function getDeleteProductUrl(){
        $productId =$this->getProduct()->getId();
        return $this->getUrl('devadmin/*/deleteproduct', ['id' => $productId]);
    }
}
