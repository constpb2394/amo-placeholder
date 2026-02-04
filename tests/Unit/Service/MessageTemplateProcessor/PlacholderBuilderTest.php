<?php

namespace Constpb\AmoPlaceholder\Tests\Unit\Service\MessageTemplateProcessor;

use Constpb\AmoPlaceholder\Entity\CustomFields\Model;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Custom;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Email;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Phone;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Entity\Enum\PhoneFieldEnum;
use Constpb\AmoPlaceholder\Placeholder\Builder\CommonPlaceholderBuilder;
use Constpb\AmoPlaceholder\Placeholder\Factory\HandlerFactory;
use Constpb\AmoPlaceholder\Placeholder\Factory\PlaceholderFactory;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PlacholderBuilderTest extends TestCase
{
    private const CUSTOM_FIELD_NAME_TEMPLATE = 'Пользовательское поле %d';
    private const CONTACT_ENTITY_NAME_PLACEHOLDER = '{{Контакт / Наименование контакта}}';
    private const LEAD_ENTITY_NAME_PLACEHOLDER = '{{Сделка / Наименование сделки}}';
    private const CONTACT_PHONE_PLACEHOLDER = '{{Контакт / Телефон}}';
    private const CONTACT_PHONE_WORK_PLACEHOLDER = '{{Контакт / Телефон - Рабочий}}';
    private const CONTACT_PHONE_WORKDD_PLACEHOLDER = '{{Контакт / Телефон - Рабочий прямой}}';

    private const ADDRESS_LINE_1_CODE = 'address_line_1';
    private const ADDRESS_LINE_2_CODE = 'address_line_2';
    private const ADDRESS_CITY_CODE = 'city';

    public function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $handlerFactory = new HandlerFactory($logger);
        $placeholderFactory = new PlaceholderFactory();

        $this->builder = new CommonPlaceholderBuilder($logger, $handlerFactory, $placeholderFactory);
    }

    public function testBuildContactCustomFieldPhoneTypeWithoutEnums(): void
    {
        $cf = new Model(
            new Phone('1', sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1)),
            null,
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::CONTACT, [$cf]);

        $this->assertIsArray($placeholders);
        $this->assertCount(2, $placeholders);
        $this->assertEquals(self::CONTACT_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals(self::CONTACT_PHONE_PLACEHOLDER, $placeholders[1]);
    }

    public function testBuildContactCustomFieldPhoneTypeWithEmptyEnums(): void
    {

        $cf = new Model(
            new Phone('1', sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1)),
            [],
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::CONTACT, [$cf]);

        $this->assertIsArray($placeholders);
        $this->assertCount(2, $placeholders);
        $this->assertEquals(self::CONTACT_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals(self::CONTACT_PHONE_PLACEHOLDER, $placeholders[1]);
    }

    public function testBuildContactCustomFieldPhoneTypeWithValidEnums(): void
    {

        $cf = new Model(
            new Phone('1', sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1)),
            [PhoneFieldEnum::AMOCRM_PHONE_WORK->value, PhoneFieldEnum::AMOCRM_PHONE_WORKDD->value],
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::CONTACT, [$cf]);

        $this->assertIsArray($placeholders);
        $this->assertCount(4, $placeholders);
        $this->assertEquals(self::CONTACT_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals(self::CONTACT_PHONE_PLACEHOLDER, $placeholders[1]);
        $this->assertEquals(self::CONTACT_PHONE_WORK_PLACEHOLDER, $placeholders[2]);
        $this->assertEquals(self::CONTACT_PHONE_WORKDD_PLACEHOLDER, $placeholders[3]);
    }

    public function testBuildContactCustomFieldPhoneTypeWithInvalidEnums(): void
    {
        $cf = new Model(
            new Phone('1', sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1)),
            [PhoneFieldEnum::AMOCRM_PHONE_WORK->value, 'WRONG'],
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::CONTACT, [$cf]);

        $this->assertIsArray($placeholders);
        $this->assertCount(3, $placeholders);
        $this->assertEquals(self::CONTACT_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals(self::CONTACT_PHONE_PLACEHOLDER, $placeholders[1]);
        $this->assertEquals(self::CONTACT_PHONE_WORK_PLACEHOLDER, $placeholders[2]);

    }

    public function testBuildLeadCustomFieldWithoutEnums(): void
    {
        $fieldName = sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1);

        $cf = new Model(
            new Custom('1', $fieldName),
            null,
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::LEAD, [$cf]);

        $expectedPlaceholder = sprintf(PlaceholderInterface::PLACEHOLDER_TEMPLATE, 'Сделка', $fieldName);

        $this->assertIsArray($placeholders);
        $this->assertCount(2, $placeholders);
        $this->assertEquals(self::LEAD_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals($expectedPlaceholder, $placeholders[1]);
    }

    public function testBuildLeadCustomFieldWithEmptyEnums(): void
    {
        $fieldName = sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1);

        $cf = new Model(
            new Custom('1', $fieldName),
            [],
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::LEAD, [$cf]);

        $expectedPlaceholder = sprintf(PlaceholderInterface::PLACEHOLDER_TEMPLATE, 'Сделка', $fieldName);

        $this->assertIsArray($placeholders);
        $this->assertCount(2, $placeholders);
        $this->assertEquals(self::LEAD_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals($expectedPlaceholder, $placeholders[1]);
    }

    public function testBuildLeadCustomFieldWithValidEnums(): void
    {
        $fieldName = sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1);

        $cf = new Model(
            new Custom('1', sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1)),
            [
                self::ADDRESS_LINE_1_CODE,
                self::ADDRESS_LINE_2_CODE,
                self::ADDRESS_CITY_CODE
            ],
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::LEAD, [$cf]);

        $expectedPlaceholder = sprintf(PlaceholderInterface::PLACEHOLDER_TEMPLATE, 'Сделка', $fieldName);

        $this->assertIsArray($placeholders);
        $this->assertCount(2, $placeholders);
        $this->assertEquals(self::LEAD_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertEquals($expectedPlaceholder, $placeholders[1]);
    }

    public function testBuildContactCustomFieldWrongTypeWithValidEnums(): void
    {

        $cf = new Model(
            new Email('1', sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1)),
            [PhoneFieldEnum::AMOCRM_PHONE_WORK->value],
        );

        $placeholders = $this->builder->buildPlaceholderList(EntityTypeEnum::CONTACT, [$cf]);

        $this->assertIsArray($placeholders);
        $this->assertCount(3, $placeholders);
        $this->assertEquals(self::CONTACT_ENTITY_NAME_PLACEHOLDER, $placeholders[0]);
        $this->assertNotEquals(self::CONTACT_PHONE_PLACEHOLDER, $placeholders[1]);
        $this->assertNotEquals(self::CONTACT_PHONE_WORK_PLACEHOLDER, $placeholders[2]);

    }
}
