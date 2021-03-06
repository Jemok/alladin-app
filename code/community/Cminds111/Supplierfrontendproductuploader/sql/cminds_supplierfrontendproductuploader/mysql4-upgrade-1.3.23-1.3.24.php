<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute("customer", "supplier_approve",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Is supplier approved",
    "input"    => "select",
    "source"   => "eav/entity_attribute_source_boolean",
    "visible"  => true,
    "required" => false,
    "default"  => "0",
    "frontend" => "",
    "unique"   => false,
    "note"     => ""

));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "supplier_approve");


$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$attribute->setData("used_in_forms", $used_in_forms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100)
;
$attribute->save();

$installer->endSetup();
