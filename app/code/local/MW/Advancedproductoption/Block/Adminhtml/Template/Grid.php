<?php

class MW_Advancedproductoption_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('advancedproductoptionGrid');
      $this->setDefaultSort('advancedproductoption_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collections = Mage::getModel('advancedproductoption/template')->getCollection();
      $this->setCollection($collections);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('template_id', array(
          'header'    => Mage::helper('advancedproductoption')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'template_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('advancedproductoption')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('advancedproductoption')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('advancedproductoption')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('advancedproductoption')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('advancedproductoption')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('advancedproductoption')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('advancedproductoption_id');
        $this->getMassactionBlock()->setFormFieldName('template_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('advancedproductoption')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('advancedproductoption')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('advancedproductoption/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('advancedproductoption')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('advancedproductoption')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}