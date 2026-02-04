<?php

namespace Constpb\AmoPlaceholder\Tests\Unit\Service\MessageTemplateProcessor;

use Constpb\AmoPlaceholder\Entity\CustomFields\Model;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Custom;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Email;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Phone;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\CustomHandler;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\EmailHandler;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\PhoneHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FieldHandlerTest extends TestCase
{
    private const CF_PHONE = 'Телефон';
    private const CF_EMAIL = 'Email';
    private const CF_LEGAL_ENTITY = 'Юр. лицо';

    public function setUp(): void
    {

    }

    public function testHandlerCustomFieldWrongType(): void
    {
        $handler = new PhoneHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Email('1', self::CF_EMAIL), ['WORK', 'WORKDD', 'WRONG']);

        $this->expectException(\InvalidArgumentException::class);

        $handler->handleCustomField($cf);
    }

    public function testPhoneHandlerCustomFieldWithoutEnum(): void
    {
        $handler = new PhoneHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Phone('1', self::CF_PHONE), null);

        $placeholders = $handler->handleCustomField($cf);
        $expected = self::CF_PHONE;

        $this->assertIsArray($placeholders);
        $this->assertCount(1, $placeholders);
        $this->assertEquals($expected, $placeholders[0]->getValue(self::CF_LEGAL_ENTITY));
    }

    public function testPhoneHandlerCustomFieldWithEnum(): void
    {
        $handler = new PhoneHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Phone('1', self::CF_PHONE), ['WORK', 'WORKDD', 'WRONG']);

        $placeholders = $handler->handleCustomField($cf);
        $expected = [
            'Телефон',
            'Телефон - Рабочий',
            'Телефон - Рабочий прямой'
        ];

        $this->assertIsArray($placeholders);
        $this->assertCount(3, $placeholders);
        $this->assertEquals($expected[0], $placeholders[0]->getValue(self::CF_LEGAL_ENTITY));
        $this->assertEquals($expected[1], $placeholders[1]->getValue(self::CF_LEGAL_ENTITY));
        $this->assertEquals($expected[2], $placeholders[2]->getValue(self::CF_LEGAL_ENTITY));
    }

    public function testEmailHandlerCustomFieldWithoutEnum(): void
    {
        $handler = new EmailHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Email('1', self::CF_EMAIL), ['WORK', 'PRIV', 'WRONG']);

        $placeholders = $handler->handleCustomField($cf);
        $expected = [
            'Email',
            'Email - Рабочий',
            'Email - Личный'
        ];

        $this->assertIsArray($placeholders);
        $this->assertCount(3, $placeholders);
        $this->assertEquals($expected[0], $placeholders[0]->getValue(self::CF_LEGAL_ENTITY));
        $this->assertEquals($expected[1], $placeholders[1]->getValue(self::CF_LEGAL_ENTITY));
        $this->assertEquals($expected[2], $placeholders[2]->getValue(self::CF_LEGAL_ENTITY));
    }

    public function testEmailHandlerCustomFieldWithEnum(): void
    {
        $handler = new EmailHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Email('1', self::CF_EMAIL), ['WORK', 'PRIV', 'WRONG']);

        $placeholders = $handler->handleCustomField($cf);
        $expected = [
            'Email',
            'Email - Рабочий',
            'Email - Личный'
        ];

        $this->assertIsArray($placeholders);
        $this->assertCount(3, $placeholders);
        $this->assertEquals($expected[0], $placeholders[0]->getValue(self::CF_LEGAL_ENTITY));
        $this->assertEquals($expected[1], $placeholders[1]->getValue(self::CF_LEGAL_ENTITY));
        $this->assertEquals($expected[2], $placeholders[2]->getValue(self::CF_LEGAL_ENTITY));
    }

    public function testCustomHandlerCustomFieldWithoutEnum(): void
    {
        $handler = new CustomHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Custom('1', self::CF_LEGAL_ENTITY), null);

        $placeholders = $handler->handleCustomField($cf);
        $expected = self::CF_LEGAL_ENTITY;

        $this->assertIsArray($placeholders);
        $this->assertCount(1, $placeholders);
        $this->assertEquals($expected, $placeholders[0]->getValue(self::CF_LEGAL_ENTITY));
    }

    public function testCustomHandlerCustomFieldWithEnum(): void
    {
        $handler = new CustomHandler($this->createMock(LoggerInterface::class));

        $cf = new Model(new Custom('1', self::CF_LEGAL_ENTITY), ['WORK', 'PRIV', 'WRONG']);

        $placeholders = $handler->handleCustomField($cf);
        $expected = self::CF_LEGAL_ENTITY;

        $this->assertIsArray($placeholders);
        $this->assertCount(1, $placeholders);
        $this->assertEquals($expected, $placeholders[0]->getValue(self::CF_LEGAL_ENTITY));
    }
}
