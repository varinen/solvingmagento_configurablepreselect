<?php
/**
 * Solvingmagento_ConfigurablePreSelect observer class
 *
 * PHP version 5.3
 *
 * @category  Solvingmagento
 * @package   Solvingmagento
 * @author    Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2013 Oleg Ishenko
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   GIT: <0.1.0>
 * @link      http://www.solvingmagento.com/
 *
 */

/** Solvingmagento_ConfigurablePreSelect_Model_Observer
 *
 * @category Solvingmagento
 * @package  Solvingmagento
 *
 * @author   Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version  Release: <package_version>
 * @link     http://www.solvingmagento.com/
 */

class Solvingmagento_ConfigurablePreSelect_Model_Observer
{
    public function preSelectConfigurable(Varien_Event_Observer $observer)
    {
        $product    = $observer->getEvent()->getProduct();
        $request    = Mage::app()->getRequest();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();

        $candidates = array();

        if (($action === 'view') && ($controller === 'product') && ($product->getTypeId() === 'configurable')) {

            $configurableAttributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);

            $usedProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);

            foreach ($usedProducts as $childProduct) {
                if (!$childProduct->isSaleable()) {
                    continue;
                }
                foreach ($configurableAttributes as $attribute) {
                    $productAttribute   = $attribute->getProductAttribute();
                    $productAttributeId = $productAttribute->getId();
                    $attributeValue     = $childProduct->getData($productAttribute->getAttributeCode());
                    $candidates[$productAttributeId] =  $attributeValue;

                }
                break;
            }
        }

        $preconfiguredValues = new Varien_Object();
        $preconfiguredValues->setData('super_attribute', $candidates);
        $product->setPreconfiguredValues($preconfiguredValues);

        return true;
    }
}