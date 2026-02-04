<?php

namespace Constpb\AmoPlaceholder\Tests\Unit\Service\MessageTemplateProcessor;

use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Phone;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\FieldType;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Constpb\AmoPlaceholder\Extractor\AgregateValueExtractor;
use Constpb\AmoPlaceholder\Extractor\SeparateValueExtractor;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    private BaseCustomFieldValuesModel $model;

    private const FIELD_ID = '1236613';
    private const FIELD_NAME = 'Телефон';

    public function setUp(): void
    {
        $this->model = BaseCustomFieldValuesModel::fromArray([
            'field_id' => 1236613,
            'field_name' => 'Телефон',
            'field_code' => 'PHONE',
            'field_type' => 'multitext',
            'values' => [
                [
                    'value' => '+1',
                    'enum_id' => 1339133,
                    'enum_code' => 'WORK',
                ],
                [
                    'value' => '+2',
                    'enum_id' => 1339133,
                    'enum_code' => 'WORKDD',
                ],
            ],
        ]);

        $this->type = new Value(
            new Phone(self::FIELD_ID, self::FIELD_NAME),
            'value',
            'enum_code'
        );
    }

    public function testCommonExtractor(): void
    {
        $extractor = new SeparateValueExtractor();

        $data = $extractor->extractData($this->model, $this->type);

        $this->assertNotEmpty($data);
        $this->assertCount(2, $data);

        $cf1= $data[0];
        $cf2= $data[1];

        $this->assertInstanceOf(Value::class, $cf1);
        $this->assertInstanceOf(Value::class, $cf2);

        $this->assertEquals(FieldTypeEnum::PHONE, $cf1->getType());
        $this->assertEquals(FieldTypeEnum::PHONE, $cf2->getType());

        $this->assertEquals('+1', $cf1->getValue());
        $this->assertEquals('WORK', $cf1->getEnumCode());
        $this->assertEquals('+2', $cf2->getValue());
        $this->assertEquals('WORKDD', $cf2->getEnumCode());
    }

    public function testCommonCustomExtractor(): void
    {
        $extractor = new AgregateValueExtractor();

        $data = $extractor->extractData($this->model, $this->type);

        $this->assertNotEmpty($data);
        $this->assertCount(1, $data);

        $cf1= $data[0];

        $this->assertInstanceOf(Value::class, $cf1);

        $this->assertEquals(FieldTypeEnum::PHONE, $cf1->getType());

        $this->assertEquals('+1, +2', $cf1->getValue());
        $this->assertNull($cf1->getEnumCode());
    }

}
