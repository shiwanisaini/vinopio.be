<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Block\Adminhtml\Attribute\Edit\Tab\Element;

use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Model\UrlFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\LayeredNavigation\Model\AttributeConfig\OptionConfig;
use Mirasvit\LayeredNavigation\Repository\AttributeConfigRepository;

/**
 * @SuppressWarnings(PHPMD)
 */
class OptionsConfig extends Widget
{
    private $attributeConfigRepository;

    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
    private $attribute;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $uploadConfig;

    private $urlFactory;

    private $eavConfig;

    public function __construct(
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        Context $context,
        Config $eavConfig,
        AttributeConfigRepository $attributeConfigRepository,
        Registry $registry
    ) {
        $this->urlFactory                = $urlFactory;
        $this->formFactory               = $formFactory;
        $this->eavConfig                 = $eavConfig;
        $this->attributeConfigRepository = $attributeConfigRepository;

        $this->attribute = $registry->registry('entity_attribute');

        parent::__construct($context);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock(Widget\Button::class)
            ->setData([
                'label' => __('Add New Row'),
                'id'    => 'add_link_item',
                'class' => 'add',
            ]);

        return $addButton->toHtml();
    }

    /**
     * @return array
     */
    public function getAttributeOptions()
    {
        $attribute = $this->_getAttribute();

        $options = [];

        foreach ($attribute->getSource()->getAllOptions() as $option) {
            if (isset($option['value']) && $option['value']) {
                $options[$option['value']] = [
                    'value' => $option['value'],
                    'name'  => $option['label'],
                ];
            }
        }

        $attrConfig = $this->attributeConfigRepository->getByAttributeCode($this->attribute->getAttributeCode());

        if ($attrConfig) {
            $optionsConfig = $attrConfig->getOptionsConfig();

            foreach ($optionsConfig as $optionConfig) {
                $optionId = $optionConfig->getOptionId();
                if (!isset($options[$optionId])) {
                    continue;
                }
                $options[$optionId][OptionConfig::LABEL]               = $optionConfig->getLabel();
                $options[$optionId][OptionConfig::IS_FULL_IMAGE_WIDTH] = $optionConfig->isFullImageWidth();

                if ($optionConfig->getImagePath()) {
                    $options[$optionId]['navigation_file_save'] = [
                        'url'  => $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                            . 'tmp/catalog/product/' . $optionConfig->getImagePath(),
                        'file' => $optionConfig->getImagePath(),
                    ];
                }
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getConfigJson()
    {
        $this->getConfig()->setUrl($this->urlFactory->create()
            ->addSessionParam()->getUrl('*/adminhtml_label/upload', ['_secure' => true]));
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters([
            'all' => [
                'label' => __('All Files'),
                'files' => ['*.*'],
            ],
        ]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);

        return SerializeService::decode($this->getConfig()->getData());
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        if ($this->uploadConfig === null) {
            $this->uploadConfig = new \Magento\Framework\DataObject();
        }

        return $this->uploadConfig;
    }

    /**
     * @param string $fieldId
     * @param string $fieldName
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageField($fieldId = 'img_field', $fieldName = 'img_field')
    {
        $form = $this->formFactory->create();
        $form->setFieldNameSuffix('label');

        $general = $form->addFieldset('fieldset_' . $fieldId, [
            'legend'  => __('Image'),
            'html_id' => 'fieldsethtml_' . $fieldId,
        ]);
        $general->addType('image1', ImageElement::class);
        $general->addField($fieldId, 'image1', [
            'label'    => __('Title'),
            'required' => true,
            'name'     => $fieldName,
            'value'    => '',
            'html_id'  => $fieldId,
        ]);

        return $general->getChildrenHtml();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Mirasvit_LayeredNavigation::product/attribute/tab/element/options_config.phtml');
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected function _getAttribute()
    {
        return $this->eavConfig->getAttribute('catalog_product', $this->attribute->getAttributeCode());
    }
}
