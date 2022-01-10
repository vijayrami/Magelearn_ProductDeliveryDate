<?php
namespace Magelearn\ProductDeliveryDate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    protected $groupCollectionFactory;
    protected $attributeFactory;
    protected $eavEntitiyType;
    public function __construct(
        \Magento\Eav\Model\Entity\Type $eavEntitiyType,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
    ) {
        $this->attributeFactory=$attributeFactory;
        $this->eavEntitiyType=$eavEntitiyType;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /* $attributeSetId=$this->eavEntitiyType->loadByCode('catalog_product')->getDefaultAttributeSetId(); */
        $attributeSetCollection=$this->eavEntitiyType->loadByCode('catalog_product')->getAttributeSetCollection();
        $entityTypeId=$this->eavEntitiyType->loadByCode('catalog_product')->getId();
        /* @var $model \Magento\Catalog\Model\Resource\Eav\Attribute */
        $model = $this->attributeFactory->create();
        $data['attribute_code'] = 'show_delivery_datepicker';
        $data['is_user_defined'] =1;
        $data['frontend_input'] = 'boolean';
        $data += ['is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => []];
        $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
        $data['default_value'] = 0;
        $data['frontend_label']='Show Delivery Datepicker';
        $model->addData($data);
        $model->setEntityTypeId($entityTypeId);
        $model->setIsUserDefined(1);
        
        foreach ($attributeSetCollection as $attributeSet) {
            $attributeSetId=$attributeSet->getId();
            $groupCollection=$this->groupCollectionFactory->create()->setAttributeSetFilter($attributeSetId)->load();
            $groupCode='product-details';
            $attributeGroupId=null;
            foreach ($groupCollection as $group) {
                if ($group->getAttributeGroupCode() == $groupCode) {
                    $attributeGroupId = $group->getAttributeGroupId();
                    break;
                }
            }
            $model->setAttributeSetId($attributeSetId);
            $model->setAttributeGroupId($attributeGroupId);
            $model->save();
        }
        
        $installer->endSetup();
    }
}
