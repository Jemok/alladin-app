<?php
class Cminds_SupplierTrading_Block_Adminhtml_Trades_Grid_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        return '<a href="'.Mage::helper("adminhtml")
            ->getUrl("adminhtml/customer/edit/id/".$row->getCustomerId()).'">'.$row->getCustomerFirstName().' '.$row->getCustomerLastName().'</a>';
    }

}
?>