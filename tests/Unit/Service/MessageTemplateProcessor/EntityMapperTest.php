<?php

namespace Constpb\AmoPlaceholder\Tests\Unit\Service\MessageTemplateProcessor;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\LegalEntityCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SmartAddressCustomFieldValuesModel;
use AmoCRM\Models\LeadModel;
use Constpb\AmoPlaceholder\Entity\Contact;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Factory\CommonTypeFactory;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Constpb\AmoPlaceholder\Entity\Lead;
use Constpb\AmoPlaceholder\Mapper\Entity\ContactMapper;
use Constpb\AmoPlaceholder\Mapper\Entity\LeadMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class EntityMapperTest extends TestCase
{
    private const ENTITY_ID = 12345;
    private const ENTITY_NAME = 'Сущность 1';

    private const PHONE_FIELD_NAME = 'Телефон';
    private const PHONE_FIELD_CODE = 'PHONE';
    private const EMAIL_FIELD_NAME = 'Email';
    private const EMAIL_FIELD_CODE = 'EMAIL';

    private const LEGAL_ENTITY_FIELD_NAME = 'Юр. лицо';
    private const LEGAL_ENTITY_NAME = 'ООО "Catcode"';
    private const LEGAL_ENTITY_VAT_ID = '503018024266';
    private const LEGAL_ENTITY_TAX = '123456789';
    private const LEGAL_ENTITY_BANK_CODE = '4266';
    private const LEGAL_ENTITY_ADDRESS = 'г. Москва';
    private const SELECT_FIELD_NAME = 'Схема оплаты';
    private const SELECT_FIELD_VALUE_1 = 'Аннуитетный';
    private const SELECT_FIELD_VALUE_2 = 'Дифференцированный';
    private const ADDRESS_FIELD_NAME = 'Адрес длинный';
    private const ADDRESS_LINE_1_CODE = 'address_line_1';
    private const ADDRESS_LINE_2_CODE = 'address_line_2';
    private const ADDRESS_CITY_CODE = 'city';
    private const ADDRESS_STATE_CODE = 'state';

    private const ADDRESS_LINE_1_VALUE = 'Карла Маркса 12';
    private const ADDRESS_LINE_2_VALUE = 'д. 7';
    private const ADDRESS_CITY_VALUE = 'Наро-Фоминск';
    private const ADDRESS_STATE_VALUE = 'Московская область';



    public function setUp(): void
    {
        $this->legalEntityCF = LegalEntityCustomFieldValuesModel::fromArray([
            'field_id' => 11,
            'field_name' => self::LEGAL_ENTITY_FIELD_NAME,
            'field_code' => null,
            'field_type' => 'legal_entity',
            'values' =>[
                [
                    'value' => [
                        'name' => self::LEGAL_ENTITY_NAME,
                        'vat_id' => self::LEGAL_ENTITY_VAT_ID,
                        'bank_code' => self::LEGAL_ENTITY_BANK_CODE,
                        'address' => self::LEGAL_ENTITY_ADDRESS,
                        'tax_registration_reason_code' => self::LEGAL_ENTITY_TAX,
                    ],
                ],
            ],
        ]);

        $this->phoneCF = MultitextCustomFieldValuesModel::fromArray([
            'field_id' => 12,
            'field_name' => self::PHONE_FIELD_NAME,
            'field_code' => self::PHONE_FIELD_CODE,
            'field_type' => 'multitext',
            'values' =>[
                [
                    'value' => '+1',
                    'enum_id' => 1,
                    'enum_code' => 'WORK',
                ],
                [
                    'value' => '+2',
                    'enum_id' => 2,
                    'enum_code' => 'WORKDD',
                ],
                [
                    'value' => '+3',
                    'enum_id' => 3,
                    'enum_code' => 'WRONG',
                ],
            ],
        ]);

        $this->emailCF = MultitextCustomFieldValuesModel::fromArray([
            'field_id' => 12,
            'field_name' => self::EMAIL_FIELD_NAME,
            'field_code' => self::EMAIL_FIELD_CODE,
            'field_type' => 'multitext',
            'values' =>[
                [
                    'value' => '+1',
                    'enum_id' => 1,
                    'enum_code' => 'WORK',
                ],
                [
                    'value' => '+2',
                    'enum_id' => 2,
                    'enum_code' => 'PRIV',
                ],
                [
                    'value' => '+3',
                    'enum_id' => 3,
                    'enum_code' => 'WRONG',
                ],
            ],
        ]);

        $this->selectCF = SelectCustomFieldValuesModel::fromArray([
            'field_id' => 13,
            'field_name' => self::SELECT_FIELD_NAME,
            'field_code' => null,
            'field_type' => 'select',
            'values' =>[
                [
                    'value' => self::SELECT_FIELD_VALUE_1,
                    'enum_id' => 1,
                    'enum_code' => null,
                ],
            ],
        ]);

        $this->multiSelectCF = MultiSelectCustomFieldValuesModel::fromArray([
            'field_id' => 14,
            'field_name' => self::SELECT_FIELD_NAME,
            'field_code' => null,
            'field_type' => 'multiselect',
            'values' =>[
                [
                    'value' => self::SELECT_FIELD_VALUE_1,
                    'enum_id' => 1,
                    'enum_code' => null,
                ],
                [
                    'value' => self::SELECT_FIELD_VALUE_2,
                    'enum_id' => 2,
                    'enum_code' => null,
                ],
            ],
        ]);

        $this->addressCF = SmartAddressCustomFieldValuesModel::fromArray([
            'field_id' => 15,
            'field_name' => self::ADDRESS_FIELD_NAME,
            'field_code' => null,
            'field_type' => 'smart_address',
            'values' =>[
                [
                    'value' => self::ADDRESS_LINE_1_VALUE,
                    'enum_id' => 1,
                    'enum_code' => self::ADDRESS_LINE_1_CODE,
                ],
                [
                    'value' => self::ADDRESS_LINE_2_VALUE,
                    'enum_id' => 2,
                    'enum_code' => self::ADDRESS_LINE_2_CODE,
                ],
                [
                    'value' => self::ADDRESS_CITY_VALUE,
                    'enum_id' => 3,
                    'enum_code' => self::ADDRESS_CITY_CODE,
                ],
                [
                    'value' => self::ADDRESS_STATE_VALUE,
                    'enum_id' => 4,
                    'enum_code' => self::ADDRESS_STATE_CODE,
                ],
            ],
        ]);

        $this->typeFactory = new CommonTypeFactory();

    }

    public function testLeadMapLegalEntity()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new LeadModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->legalEntityCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Lead::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $cf = $cfs[0];

        $this->assertInstanceOf(Value::class, $cf);

        $expectedValue = implode(', ', [
            self::LEGAL_ENTITY_NAME,
            self::LEGAL_ENTITY_VAT_ID,
            self::LEGAL_ENTITY_TAX,
            self::LEGAL_ENTITY_ADDRESS,
            self::LEGAL_ENTITY_BANK_CODE
        ]);

        $this->assertEquals($expectedValue, $cf->getValue());
    }

    public function testLeadMapPhoneField()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new LeadModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->phoneCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Lead::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(3, $cfs);

        $workPhone = $cfs[0];
        $workDDPhone = $cfs[1];
        $wrongPhone = $cfs[2];

        $this->assertInstanceOf(Value::class, $workPhone);
        $this->assertInstanceOf(Value::class, $workDDPhone);

        $this->assertEquals(self::PHONE_FIELD_NAME, $workPhone->getName());
        $this->assertEquals(self::PHONE_FIELD_NAME, $workDDPhone->getName());
        $this->assertEquals(self::PHONE_FIELD_NAME, $wrongPhone->getName());

        $this->assertEquals('+1', $workPhone->getValue());
        $this->assertEquals('+2', $workDDPhone->getValue());
        $this->assertEquals('+3', $wrongPhone->getValue());

        $this->assertEquals('WORK', $workPhone->getEnumCode());
        $this->assertEquals('WORKDD', $workDDPhone->getEnumCode());
        $this->assertEquals('WRONG', $wrongPhone->getEnumCode());

        $this->assertEquals(FieldTypeEnum::PHONE, $workPhone->getType());
        $this->assertEquals(FieldTypeEnum::PHONE, $workDDPhone->getType());
        $this->assertEquals(FieldTypeEnum::PHONE, $wrongPhone->getType());
    }

    public function testLeadMapEmailField()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new LeadModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->emailCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Lead::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(3, $cfs);

        $workPhone = $cfs[0];
        $workDDPhone = $cfs[1];
        $wrongPhone = $cfs[2];

        $this->assertInstanceOf(Value::class, $workPhone);
        $this->assertInstanceOf(Value::class, $workDDPhone);

        $this->assertEquals(self::EMAIL_FIELD_NAME, $workPhone->getName());
        $this->assertEquals(self::EMAIL_FIELD_NAME, $workDDPhone->getName());
        $this->assertEquals(self::EMAIL_FIELD_NAME, $wrongPhone->getName());

        $this->assertEquals('+1', $workPhone->getValue());
        $this->assertEquals('+2', $workDDPhone->getValue());
        $this->assertEquals('+3', $wrongPhone->getValue());

        $this->assertEquals('WORK', $workPhone->getEnumCode());
        $this->assertEquals('PRIV', $workDDPhone->getEnumCode());
        $this->assertEquals('WRONG', $wrongPhone->getEnumCode());

        $this->assertEquals(FieldTypeEnum::EMAIL, $workPhone->getType());
        $this->assertEquals(FieldTypeEnum::EMAIL, $workDDPhone->getType());
        $this->assertEquals(FieldTypeEnum::EMAIL, $wrongPhone->getType());
    }

    public function testLeadMapSelectField()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new LeadModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->selectCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Lead::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $value = $cfs[0];

        $this->assertInstanceOf(Value::class, $value);

        $this->assertEquals(self::SELECT_FIELD_VALUE_1, $value->getValue());
        $this->assertEquals(self::SELECT_FIELD_NAME, $value->getName());
        $this->assertNull($value->getEnumCode());
        $this->assertEquals(FieldTypeEnum::CUSTOM, $value->getType());
    }

    public function testLeadMapMultiselectField()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new LeadModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->multiSelectCF);
        // $customFields->add($this->addressCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Lead::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $value = $cfs[0];

        $this->assertInstanceOf(Value::class, $value);

        $expectedValue = implode(', ', [self::SELECT_FIELD_VALUE_1, self::SELECT_FIELD_VALUE_2]);

        $this->assertEquals($expectedValue, $value->getValue());
        $this->assertEquals(self::SELECT_FIELD_NAME, $value->getName());
        $this->assertNull($value->getEnumCode());
        $this->assertEquals(FieldTypeEnum::CUSTOM, $value->getType());
    }

    public function testLeadMapAddressField()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new LeadModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->addressCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Lead::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $value = $cfs[0];

        $this->assertInstanceOf(Value::class, $value);

        $expectedValue = implode(', ', [
            self::ADDRESS_LINE_1_VALUE,
            self::ADDRESS_LINE_2_VALUE,
            self::ADDRESS_CITY_VALUE,
            self::ADDRESS_STATE_VALUE,
        ]);

        $this->assertEquals($expectedValue, $value->getValue());
        $this->assertEquals(self::ADDRESS_FIELD_NAME, $value->getName());
        $this->assertNull($value->getEnumCode());
        $this->assertEquals(FieldTypeEnum::CUSTOM, $value->getType());
    }

    public function testContactMapLegalEntity()
    {
        $mapper = new ContactMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->legalEntityCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Contact::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $cf = $cfs[0];

        $this->assertInstanceOf(Value::class, $cf);

        $expectedValue = implode(', ', [
            self::LEGAL_ENTITY_NAME,
            self::LEGAL_ENTITY_VAT_ID,
            self::LEGAL_ENTITY_TAX,
            self::LEGAL_ENTITY_ADDRESS,
            self::LEGAL_ENTITY_BANK_CODE
        ]);

        $this->assertEquals($expectedValue, $cf->getValue());
    }

    public function testContactMapPhoneField()
    {
        $mapper = new ContactMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->phoneCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Contact::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(3, $cfs);

        $workPhone = $cfs[0];
        $workDDPhone = $cfs[1];
        $wrongPhone = $cfs[2];

        $this->assertInstanceOf(Value::class, $workPhone);
        $this->assertInstanceOf(Value::class, $workDDPhone);
        $this->assertInstanceOf(Value::class, $wrongPhone);

        $this->assertEquals(self::PHONE_FIELD_NAME, $workPhone->getName());
        $this->assertEquals(self::PHONE_FIELD_NAME, $workDDPhone->getName());
        $this->assertEquals(self::PHONE_FIELD_NAME, $wrongPhone->getName());

        $this->assertEquals('+1', $workPhone->getValue());
        $this->assertEquals('+2', $workDDPhone->getValue());
        $this->assertEquals('+3', $wrongPhone->getValue());

        $this->assertEquals('WORK', $workPhone->getEnumCode());
        $this->assertEquals('WORKDD', $workDDPhone->getEnumCode());
        $this->assertEquals('WRONG', $wrongPhone->getEnumCode());

        $this->assertEquals(FieldTypeEnum::PHONE, $workPhone->getType());
        $this->assertEquals(FieldTypeEnum::PHONE, $workDDPhone->getType());
        $this->assertEquals(FieldTypeEnum::PHONE, $wrongPhone->getType());

    }

    public function testContactMapEmailField()
    {
        $mapper = new ContactMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->emailCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Contact::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(3, $cfs);

        $workPhone = $cfs[0];
        $workDDPhone = $cfs[1];
        $wrongPhone = $cfs[2];

        $this->assertInstanceOf(Value::class, $workPhone);
        $this->assertInstanceOf(Value::class, $workDDPhone);

        $this->assertEquals(self::EMAIL_FIELD_NAME, $workPhone->getName());
        $this->assertEquals(self::EMAIL_FIELD_NAME, $workDDPhone->getName());
        $this->assertEquals(self::EMAIL_FIELD_NAME, $wrongPhone->getName());

        $this->assertEquals('+1', $workPhone->getValue());
        $this->assertEquals('+2', $workDDPhone->getValue());
        $this->assertEquals('+3', $wrongPhone->getValue());

        $this->assertEquals('WORK', $workPhone->getEnumCode());
        $this->assertEquals('PRIV', $workDDPhone->getEnumCode());
        $this->assertEquals('WRONG', $wrongPhone->getEnumCode());

        $this->assertEquals(FieldTypeEnum::EMAIL, $workPhone->getType());
        $this->assertEquals(FieldTypeEnum::EMAIL, $workDDPhone->getType());
        $this->assertEquals(FieldTypeEnum::EMAIL, $wrongPhone->getType());
    }

    public function testContactMapSelectField()
    {
        $mapper = new ContactMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->selectCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Contact::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $value = $cfs[0];

        $this->assertInstanceOf(Value::class, $value);

        $this->assertEquals(self::SELECT_FIELD_VALUE_1, $value->getValue());
        $this->assertEquals(self::SELECT_FIELD_NAME, $value->getName());
        $this->assertNull($value->getEnumCode());
        $this->assertEquals(FieldTypeEnum::CUSTOM, $value->getType());
    }

    public function testContactMapMultiselectField()
    {
        $mapper = new ContactMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->multiSelectCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Contact::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $value = $cfs[0];

        $this->assertInstanceOf(Value::class, $value);

        $expectedValue = implode(', ', [self::SELECT_FIELD_VALUE_1, self::SELECT_FIELD_VALUE_2]);

        $this->assertEquals($expectedValue, $value->getValue());
        $this->assertEquals(self::SELECT_FIELD_NAME, $value->getName());
        $this->assertNull($value->getEnumCode());
        $this->assertEquals(FieldTypeEnum::CUSTOM, $value->getType());
    }

    public function testContactMapAddressField()
    {
        $mapper = new ContactMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->addressCF);

        $model->setCustomFieldsValues($customFields);

        $entity = $mapper->map($model);

        $this->assertInstanceOf(Contact::class, $entity);
        $this->assertEquals(self::ENTITY_NAME, $entity->getName());

        $cfs = $entity->getCustomFields();

        $this->assertIsArray($cfs);
        $this->assertCount(1, $cfs);

        $value = $cfs[0];

        $this->assertInstanceOf(Value::class, $value);

        $expectedValue = implode(', ', [
            self::ADDRESS_LINE_1_VALUE,
            self::ADDRESS_LINE_2_VALUE,
            self::ADDRESS_CITY_VALUE,
            self::ADDRESS_STATE_VALUE,
        ]);

        $this->assertEquals($expectedValue, $value->getValue());
        $this->assertEquals(self::ADDRESS_FIELD_NAME, $value->getName());
        $this->assertNull($value->getEnumCode());
        $this->assertEquals(FieldTypeEnum::CUSTOM, $value->getType());
    }

    public function testMapperWrongEntity()
    {
        $mapper = new LeadMapper($this->typeFactory,$this->createMock(LoggerInterface::class));

        $model = new ContactModel();
        $model->setName(self::ENTITY_NAME);
        $model->setId(self::ENTITY_ID);

        $customFields = new CustomFieldsValuesCollection();
        $customFields->add($this->phoneCF);

        $this->expectException(\InvalidArgumentException::class);

        $mapper->map($model);
    }
}
