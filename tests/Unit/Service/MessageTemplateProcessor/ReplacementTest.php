<?php

namespace Constpb\AmoPlaceholder\Tests\Unit\Service\MessageTemplateProcessor;

use Constpb\AmoPlaceholder\Entity\Contact;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Custom;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Phone;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Placeholder\Factory\HandlerFactory;
use Constpb\AmoPlaceholder\Placeholder\Factory\PlaceholderFactory;
use Constpb\AmoPlaceholder\Placeholder\Replacement\CommonReplacer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ReplacementTest extends TestCase
{
    private const PHONE_FIELD_NAME = 'Телефон';
    private const CONTACT_NAME_VALUE = 'Костя';

    private const PHONE_WORK_VALUE = '+1';
    private const PHONE_WORKDD_VALUE = '+2';
    private const PHONE_WORK_CODE = 'WORK';
    private const PHONE_WORKDD_CODE = 'WORKDD';
    private const PHONE_WRONG_CODE = 'WRONG';
    private const CUSTOM_FIELD_VALUE_1 = 'Пользовательское значение';
    private const CUSTOM_FIELD_VALUE_2 = 'Пользовательское значение 2';

    private const CUSTOM_FIELD_NAME_TEMPLATE = 'Пользовательское поле %d';
    private const CONTACT_ENTITY_NAME_PLACEHOLDER = '{{Контакт / Наименование контакта}}';
    private const LEAD_ENTITY_NAME_PLACEHOLDER = '{{Сделка / Наименование сделки}}';
    private const CONTACT_PHONE_PLACEHOLDER = '{{Контакт / Телефон}}';
    private const CONTACT_PHONE_WORK_PLACEHOLDER = '{{Контакт / Телефон - Рабочий}}';
    private const CONTACT_PHONE_WORKDD_PLACEHOLDER = '{{Контакт / Телефон - Рабочий прямой}}';

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->handlerFactory = new HandlerFactory($this->logger);
        $this->placeholderFactory = new PlaceholderFactory();

        $this->cfEmptyPhone = new Value(new Phone('1', self::PHONE_FIELD_NAME), null,null);
        $this->cfWorkPhoneWithoutEnum = new Value(
            new Phone('1', self::PHONE_FIELD_NAME),
            self::PHONE_WORK_VALUE,
            null
        );
        $this->cfWorkPhone = new Value(
            new Phone('2', self::PHONE_FIELD_NAME),
            self::PHONE_WORK_VALUE,
            self::PHONE_WORK_CODE
        );
        $this->cfWorkDDPhone = new Value(
            new Phone('3', self::PHONE_FIELD_NAME),
            self::PHONE_WORKDD_VALUE,
            self::PHONE_WORKDD_CODE
        );
        $this->cfWorkPhoneWithInalidEnum = new Value(
            new Phone('3', self::PHONE_FIELD_NAME),
            self::PHONE_WORK_VALUE,
            self::PHONE_WRONG_CODE
        );

        $this->placeholders_1 = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER,
            self::LEAD_ENTITY_NAME_PLACEHOLDER,
            self::CONTACT_PHONE_PLACEHOLDER,
            self::CONTACT_PHONE_WORK_PLACEHOLDER,
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER
        ];

        $this->customName1 = sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 1);
        $this->customName2 = sprintf(self::CUSTOM_FIELD_NAME_TEMPLATE, 2);
        $this->customPlaceholder1 = sprintf('{{Контакт / %s}}', $this->customName1);
        $this->customPlaceholder2 = sprintf('{{Контакт / %s}}', $this->customName2);

        $this->cfCustomWithoutEnum = new Value(
            new Custom('3', $this->customName1),
            self::CUSTOM_FIELD_VALUE_1,
            null
        );

        $this->cfCustomWithEnum = new Value(
            new Custom('3', $this->customName2),
            self::CUSTOM_FIELD_VALUE_2,
            'ENUM'
        );

        $this->placeholders_2 = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER,
            self::LEAD_ENTITY_NAME_PLACEHOLDER,
            self::CONTACT_PHONE_PLACEHOLDER,
            self::CONTACT_PHONE_WORK_PLACEHOLDER,
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER,
            $this->customPlaceholder1,
            $this->customPlaceholder2,
        ];
    }

    public function testReplaceContactWithEmptyPhone(): void
    {
        $entity = new Contact();
        $entity->setName(self::CONTACT_NAME_VALUE);
        $entity->addCustomField($this->cfEmptyPhone);

        $replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $replacements = $replacer->getReplacements($entity, $this->placeholders_1);

        $expected = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER => self::CONTACT_NAME_VALUE,
            self::LEAD_ENTITY_NAME_PLACEHOLDER => '',
            self::CONTACT_PHONE_PLACEHOLDER => '',
            self::CONTACT_PHONE_WORK_PLACEHOLDER => '',
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER => ''
        ];

        $this->assertEqualsCanonicalizing($expected, $replacements);
    }

    public function testReplaceContactWithPhoneWithoutEnum(): void
    {
        $entity = new Contact();
        $entity->setName(self::CONTACT_NAME_VALUE);
        $entity->addCustomField($this->cfWorkPhoneWithoutEnum);

        $replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $replacements = $replacer->getReplacements($entity, $this->placeholders_1);

        $expected = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER => self::CONTACT_NAME_VALUE,
            self::LEAD_ENTITY_NAME_PLACEHOLDER => '',
            self::CONTACT_PHONE_PLACEHOLDER => self::PHONE_WORK_VALUE,
            self::CONTACT_PHONE_WORK_PLACEHOLDER => '',
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER => ''
        ];

        $this->assertEqualsCanonicalizing($expected, $replacements);
    }

    public function testReplaceContactWithPhoneWithValidEnum(): void
    {
        $entity = new Contact();
        $entity->setName(self::CONTACT_NAME_VALUE);
        $entity->addCustomField($this->cfWorkPhone);

        $replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $replacements = $replacer->getReplacements($entity, $this->placeholders_1);

        $expected = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER => self::CONTACT_NAME_VALUE,
            self::LEAD_ENTITY_NAME_PLACEHOLDER => '',
            self::CONTACT_PHONE_PLACEHOLDER => self::PHONE_WORK_VALUE,
            self::CONTACT_PHONE_WORK_PLACEHOLDER => self::PHONE_WORK_VALUE,
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER => ''
        ];

        $this->assertEqualsCanonicalizing($expected, $replacements);
    }

    public function testReplaceContactWithMultiplePhonesValidEnum(): void
    {
        $entity = new Contact();
        $entity->setName(self::CONTACT_NAME_VALUE);
        $entity->addCustomField($this->cfWorkPhone);
        $entity->addCustomField($this->cfWorkDDPhone);

        $replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $replacements = $replacer->getReplacements($entity, $this->placeholders_1);

        $expected = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER => self::CONTACT_NAME_VALUE,
            self::LEAD_ENTITY_NAME_PLACEHOLDER => '',
            self::CONTACT_PHONE_PLACEHOLDER => self::PHONE_WORK_VALUE,
            self::CONTACT_PHONE_WORK_PLACEHOLDER => self::PHONE_WORK_VALUE,
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER => self::PHONE_WORKDD_VALUE
        ];

        $this->assertEqualsCanonicalizing($expected, $replacements);
    }

    public function testReplaceContactWithMultipleCustomFields(): void
    {
        $entity = new Contact();
        $entity->setName(self::CONTACT_NAME_VALUE);
        $entity->addCustomField($this->cfCustomWithEnum);
        $entity->addCustomField($this->cfCustomWithoutEnum);

        $replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $replacements = $replacer->getReplacements($entity, $this->placeholders_2);

        $expected = [
            self::CONTACT_ENTITY_NAME_PLACEHOLDER => self::CONTACT_NAME_VALUE,
            self::LEAD_ENTITY_NAME_PLACEHOLDER => '',
            self::CONTACT_PHONE_PLACEHOLDER => '',
            self::CONTACT_PHONE_WORK_PLACEHOLDER => '',
            self::CONTACT_PHONE_WORKDD_PLACEHOLDER => '',
            $this->customPlaceholder1 => self::CUSTOM_FIELD_VALUE_1,
            $this->customPlaceholder2 => self::CUSTOM_FIELD_VALUE_2
        ];

        $this->assertEqualsCanonicalizing($expected, $replacements);
    }
}
