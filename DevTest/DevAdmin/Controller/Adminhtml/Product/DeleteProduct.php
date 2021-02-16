<?php

namespace DevTest\DevAdmin\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;

class DeleteProduct extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository = null,
        LoggerInterface $logger = null
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository ?:
            \Magento\Framework\App\ObjectManager::getInstance()->create(ProductRepositoryInterface::class);
        $this->logger = $logger ?:
            \Magento\Framework\App\ObjectManager::getInstance()->create(LoggerInterface::class);
        parent::__construct($context, $productBuilder);
    }

    /**
     * Mass Delete Action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        error_log(print_r($this->getRequest()->getParams(), true), 3, BP . "/var/log/testDeleteProduct.log");
        $productId = $this->getRequest()->getParam('id');
        $product = $this->productRepository->getById($productId);
        $productDeleted = 0;
        $productDeletedError = 0;

        try {
            $this->productRepository->delete($product);
            $productDeleted++;
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage());
            $productDeletedError++;
        }

        if ($productDeleted) {
            $this->messageManager->addSuccessMessage(
                __('The product has been deleted.')
            );
        }

        if ($productDeletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'Failed to delete the product. Please see server logs for more details.'
                )
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
    }
}
