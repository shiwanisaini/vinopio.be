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



namespace Mirasvit\LayeredNavigation\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Repository\AttributeConfigRepository;

class Navigation extends Generic implements TabInterface
{
    private $attributeConfigRepository;


    private $formFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private $attribute;

    public function __construct(
        AttributeConfigRepository $attributeConfigRepository,
        Context $context,
        Registry $registry,
        FormFactory $formFactory
    ) {
        $this->attributeConfigRepository = $attributeConfigRepository;
        $this->formFactory               = $formFactory;

        $this->attribute = $registry->registry('entity_attribute');

        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Layered Navigation');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'                => 'edit_form',
            'action'            => $this->getData('action'),
            'method'            => 'post',
            'enctype'           => 'multipart/form-data',
            'field_name_suffix' => 'attribute_config',
        ]);

        $attributeConfig = $this->getAttributeConfig();

        if (!$attributeConfig) {
            $form->addFieldset('base_fieldset', [
                'legend' => __('Layered Navigation configuration will be available after attribute creation'),
                'class'  => 'fieldset-wide',
            ]);

            $this->setForm($form);

            return parent::_prepareForm();
        }

        $frontendInput = $this->attribute->getFrontendInput();

        $form->addField(AttributeConfigInterface::ATTRIBUTE_CODE, 'hidden', [
            'name'  => AttributeConfigInterface::ATTRIBUTE_CODE,
            'value' => $attributeConfig->getAttributeCode(),
        ]);

        $form->addField('display', Fieldset\DisplayFieldset::class, [
            AttributeConfigInterface::class => $attributeConfig,
            Attribute::class                => $this->attribute,
        ]);

        $form->addField('visibility', Fieldset\VisibilityFieldset::class, [
            AttributeConfigInterface::class => $attributeConfig,
            Attribute::class                => $this->attribute,
        ]);

        $form->addField('misc', Fieldset\MiscFieldset::class, [
            AttributeConfigInterface::class => $attributeConfig,
            Attribute::class                => $this->attribute,
        ]);

        if (in_array($frontendInput, ['select', 'multiselect'])) {
            $options = $dependence = $this->getLayout()->createBlock(Element\OptionsConfig::class);

            $this->setChild('form_after', $options);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return AttributeConfigInterface|false
     */
    protected function getAttributeConfig()
    {
        if (!$this->attribute->getId()) {
            return false;
        }

        $settings = $this->attributeConfigRepository->getByAttributeCode($this->attribute->getAttributeCode());

        if (!$settings) {
            $settings = $this->attributeConfigRepository->create();
            $settings->setAttributeCode($this->attribute->getAttributeCode())
                ->setAttributeId($this->attribute->getId());
        }

        return $settings;
    }
}
