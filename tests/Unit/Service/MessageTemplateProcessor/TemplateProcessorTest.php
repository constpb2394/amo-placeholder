<?php

namespace Constpb\AmoPlaceholder\Tests\Unit\Service\MessageTemplateProcessor;

use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Factory\CommonTypeFactory;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Mapper\CustomField\CustomFieldMapper;
use Constpb\AmoPlaceholder\Mapper\Entity\Factory\CommonEntityMapperFactory;
use Constpb\AmoPlaceholder\MessageTemplateProcessorService;
use Constpb\AmoPlaceholder\Placeholder\Builder\CommonPlaceholderBuilder;
use Constpb\AmoPlaceholder\Placeholder\Factory\HandlerFactory;
use Constpb\AmoPlaceholder\Placeholder\Factory\PlaceholderFactory;
use Constpb\AmoPlaceholder\Placeholder\Replacement\CommonReplacer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TemplateProcessorTest extends TestCase
{
    private const ENTITY_NAME = 'Имя сущности';

    private const WORK_CODE = 'WORK';
    private const WORKDD_CODE = 'WORKDD';
    private const MOB_CODE = 'MOB';
    private const OTHER_CODE = 'OTHER';
    private const PRIV_CODE = 'PRIV';
    private const WRONG_CODE = 'WRONG';
    private const CF_VALUE_1 ='Не должен';
    private const CF_EMAIL_WORK_VALUE ='Рабочий имейл';
    private const CF_EMAIL_PRIV_VALUE ='Личный имейл';
    private const CF_EMAIL_OTHER_VALUE ='Другой имейл';
    private const CF_SELECT_VALUE ='Гибкая рассрочка';
    private const CF_MULTISELECT_VALUE_1 ='Вариант 1';
    private const CF_MULTISELECT_VALUE_2 ='Вариант 2';

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->typeFactory = new CommonTypeFactory();

        $this->handlerFactory = new HandlerFactory($this->logger);
        $this->placeholderFactory = new PlaceholderFactory();

        $this->placeholderBuilder = new CommonPlaceholderBuilder($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $this->customFieldMapper = new CustomFieldMapper($this->logger);
        $this->entityMapperFactory = new CommonEntityMapperFactory( $this->typeFactory, $this->logger);
        $this->replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory);

        $this->templateProcessor = new MessageTemplateProcessorService(
            $this->logger,
            $this->placeholderBuilder,
            $this->customFieldMapper,
            $this->entityMapperFactory,
            $this->replacer
        );

        $this->template = trim('1. Контакт наименование: {{Контакт / Наименование контакта}}
            2. Контакт Должность: {{Контакт / Должность}}
            3. Контакт Телефон рабочий: {{Контакт / Телефон - Рабочий}}
            4. Контакт Телефон мобильный: {{Контакт / Телефон - Мобильный}}
            5. Контакт Телефон другой: {{Контакт / Телефон - Другой}}
            6. Контакт Имейл рабочий: {{Контакт / Email - Рабочий}}
            7. Контакт Имейл личный: {{Контакт / Email - Личный}}
            8. Контакт Схема оплат: {{Контакт / Схема оплат}}
            9. Сделка Имейл рабочий: {{Сделка / Email - Рабочий}}
            10. Сделка наименование: {{Сделка / Наименование  сделки}}
            11. Сделка Схема оплат: {{Сделка / Схема оплат}}
            12. Сделка Мультисписок: {{Cделка / Мультисписок}}
            13. Сделка Юр. лицо: {{Сделка / Юр. лицо}}');
    }

    public function testProcessCommon()
    {

        $contact = new ContactModel();
        $contact->setId(1);
        $contact->setName(self::ENTITY_NAME);

        $cfValues = CustomFieldsValuesCollection::fromArray([
            [
                'field_id' => 1236613,
                'field_name' => 'Телефон',
                'field_code' => 'PHONE',
                'field_type' => 'multitext',
                'values' => [
                    [
                        'value' => '+1',
                        'enum_id' => 1339133,
                        'enum_code' => self::WORK_CODE,
                    ],
                    [
                        'value' => '+2',
                        'enum_id' => 1339133,
                        'enum_code' => self::WORKDD_CODE,
                    ],
                    [
                        'value' => '+3',
                        'enum_id' => 1339133,
                        'enum_code' => self::MOB_CODE,
                    ],
                    [
                        'value' => '+4',
                        'enum_id' => 1339133,
                        'enum_code' => self::OTHER_CODE,
                    ],
                    [
                        'value' => '+5',
                        'enum_id' => 1339133,
                        'enum_code' => self::WRONG_CODE,
                    ],
                ],
            ], [
                'field_id' => 1236614,
                'field_name' => 'Должность',
                'field_code' => 'POSITION',
                'field_type' => 'text',
                'values' => [
                    [
                        'value' => self::CF_VALUE_1,
                    ],
                ],
            ], [
                'field_id' => 1236615,
                'field_name' => 'Email',
                'field_code' => 'EMAIL',
                'field_type' => 'multitext',
                'values' => [
                    [
                        'value' => self::CF_EMAIL_WORK_VALUE,
                        'enum_id' => 1339133,
                        'enum_code' => self::WORK_CODE,
                    ],
                    [
                        'value' => self::CF_EMAIL_OTHER_VALUE,
                        'enum_id' => 1339133,
                        'enum_code' => self::OTHER_CODE,
                    ],
                    [
                        'value' => self::CF_EMAIL_PRIV_VALUE,
                        'enum_id' => 1339133,
                        'enum_code' => self::PRIV_CODE,
                    ],
                    [
                        'value' => 'WRONG EMAIL',
                        'enum_id' => 1339133,
                        'enum_code' => self::WRONG_CODE,
                    ],
                ],
            ], [
                'field_id' => 1236616,
                'field_name' => 'Схема оплат',
                'field_code' => null,
                'field_type' => 'select',
                'values' => [
                    'value' => [
                        'value' => self::CF_SELECT_VALUE,
                        'enum_id' => null,
                        'enum_code' => null,
                    ],
                ],
            ], [
                'field_id' => 1236617,
                'field_name' => 'Мультисписок',
                'field_code' => null,
                'field_type' => 'multiselect',
                'values' => [
                        [
                        'value' => self::CF_MULTISELECT_VALUE_1,
                        'enum_id' => null,
                        'enum_code' => null,
                    ], [
                        'value' => self::CF_MULTISELECT_VALUE_2,
                        'enum_id' => null,
                        'enum_code' => null,
                    ],
                ],
            ], [
                'field_id' => 1533395,
                'field_name' => 'Юр. лицо',
                'field_code' => null,
                'field_type' => 'legal_entity',
                'values' => [
                    [
                        'value'=> [
                            'name' => 'ООО Кэткод',
                            'entity_type' => null,
                            'vat_id' => '1212',
                            'tax_registration_reason_code' => null,
                            'address' => null,
                            'kpp' => '54321',
                            'bank_code' => null,
                            'external_uid' => null,
                            'unp' => null,
                            'bin' => null,
                            'egrpou' => null,
                            'real_address' => null,
                            'mfo' => null,
                            'bank_account_number' => null,
                            'oked' => null,
                            'director' => null,
                        ],
                    ],
                ],
            ],
        ]);

        $contact->setCustomFieldsValues($cfValues);

        $message = $this->templateProcessor->replaceVariables($this->template, $contact);

        $entityName = self::ENTITY_NAME;
        $cfPosition = self::CF_VALUE_1;
        $cfEmailWork = self::CF_EMAIL_WORK_VALUE;
        $cfEmailPriv = self::CF_EMAIL_PRIV_VALUE;
        $cfSelect = self::CF_SELECT_VALUE;

        $expected = trim(sprintf("1. Контакт наименование: %s
            2. Контакт Должность: %s
            3. Контакт Телефон рабочий: +1
            4. Контакт Телефон мобильный: +3
            5. Контакт Телефон другой: +4
            6. Контакт Имейл рабочий: %s
            7. Контакт Имейл личный: %s
            8. Контакт Схема оплат: %s
            9. Сделка Имейл рабочий: {{Сделка / Email - Рабочий}}
            10. Сделка наименование: {{Сделка / Наименование  сделки}}
            11. Сделка Схема оплат: {{Сделка / Схема оплат}}
            12. Сделка Мультисписок: {{Cделка / Мультисписок}}
            13. Сделка Юр. лицо: {{Сделка / Юр. лицо}}",
            $entityName,
            $cfPosition,
            $cfEmailWork,
            $cfEmailPriv,
            $cfSelect
        ));

        $this->assertEquals($expected, trim($message));

        $lead = new LeadModel();
        $lead->setId(1);
        $lead->setName(self::ENTITY_NAME);

        $lead->setCustomFieldsValues($cfValues);

        $message = $this->templateProcessor->replaceVariables($this->template, $lead);

        $cdMultiselect = self::CF_MULTISELECT_VALUE_1;

        $expected = trim(sprintf("1. Контакт наименование: %s
            2. Контакт Должность: %s
            3. Контакт Телефон рабочий: +1
            4. Контакт Телефон мобильный: +3
            5. Контакт Телефон другой: +4
            6. Контакт Имейл рабочий: %s
            7. Контакт Имейл личный: %s
            8. Контакт Схема оплат: %s
            9. Сделка Имейл рабочий: %s
            10. Сделка наименование: %s
            11. Сделка Схема оплат: %s
            12. Сделка Мультисписок: %s
            13. Сделка Юр. лицо: %s",
            $entityName,
            $cfPosition,
            $cfEmailWork,
            $cfEmailPriv,
            $cfSelect,
            $cfEmailWork,
            $entityName,
            $cfSelect,
            $cdMultiselect,
            implode(', ', ['ООО Кэткод', '1212', '54321'])
        ));
    }

    public function testGetContactVariables():void
    {
        $cfs = CustomFieldsCollection::fromArray([
            [
                "id"=> 1236611,
                "account_id" => 32647906,
                "name"=> "Должность",
                "type"=> "text",
                "code"=> "POSITION",
                "sort"=> 503,
                "is_api_only"=> false,
                "enums"=> null,
                "entity_type"=> "contacts",
            ], [
                "id"=> 1236612,
                "account_id" => 32647906,
                "name"=> "Телефон",
                "type"=> "multitext",
                "code"=> "PHONE",
                "sort"=> 504,
                "is_api_only"=> false,
                "entity_type"=> "contacts",
                "enums"=> [
                    [
                        "id"=> 1339131,
                        "value"=> "WORK",
                        "sort"=> 2
                    ],
                    [
                        "id"=> 1339133,
                        "value"=> "WORKDD",
                        "sort"=> 4
                    ],
                    [
                        "id"=> 1339135,
                        "value"=> "MOB",
                        "sort"=> 6
                    ],
                    [
                        "id"=> 1339137,
                        "value"=> "FAX",
                        "sort"=> 8
                    ],
                    [
                        "id"=> 1339139,
                        "value"=> "HOME",
                        "sort"=> 10
                    ],
                    [
                        "id"=> 1339141,
                        "value"=> "OTHER",
                        "sort"=> 12
                    ]
                ],
            ], [
                "id"=> 1236611,
                "name"=> "Список",
                "account_id" => 32647906,
                "type"=> "multiselect",
                "code"=> null,
                "sort"=> 505,
                "is_api_only"=> false,
                "entity_type"=> "contacts",
                "enums" => [
                     [
                    "id"=> 1524237,
                    "value"=> "Вариант 1",
                    "sort"=> 500
                    ], [
                        "id"=> 1524239,
                        "value"=> "Вариант 2",
                        "sort"=> 1
                    ],
                ]
            ], [
                "id"=> 1236618,
                "account_id" => 32647906,
                "name"=> "Системное",
                "type"=> "text",
                "code"=> null,
                "sort"=> 505,
                "is_api_only"=> true,
                "entity_type"=> "contacts",
                "enums" => [
                    [
                        "id"=> 1524237,
                        "value"=> "Вариант 1",
                        "sort"=> 500
                    ], [
                        "id"=> 1524239,
                        "value"=> "Вариант 2",
                        "sort"=> 1
                    ],
                ]
            ]
        ]);

        $variables = $this->templateProcessor->getVariables(EntityTypeEnum::CONTACT, $cfs);

        $expected = [
            [
                'title' => 'Контакт / Наименование контакта',
                'value' => '{{Контакт / Наименование контакта}}'
            ],
            [
                'title' => 'Контакт / Должность',
                'value' => '{{Контакт / Должность}}'
            ],
            [
                'title' => 'Контакт / Телефон',
                'value' => '{{Контакт / Телефон}}'
            ],
            [
                'title' => 'Контакт / Телефон - Рабочий',
                'value' => '{{Контакт / Телефон - Рабочий}}'
            ],
            [
                'title' => 'Контакт / Телефон - Рабочий прямой',
                'value' => '{{Контакт / Телефон - Рабочий прямой}}'
            ],
            [
                'title' => 'Контакт / Телефон - Мобильный',
                'value' => '{{Контакт / Телефон - Мобильный}}'
            ],
            [
                'title' => 'Контакт / Телефон - Домашний',
                'value' => '{{Контакт / Телефон - Домашний}}'
            ],
            [
                'title' => 'Контакт / Телефон - Другой',
                'value' => '{{Контакт / Телефон - Другой}}'
            ],
            [
                'title' => 'Контакт / Список',
                'value' => '{{Контакт / Список}}'
            ],
        ];

        $this->assertCount(9, $expected);
        $this->assertEquals($expected, $variables);
    }
}
