## Lib purposes
Main purpose of this lib is to provide functionality of creating text messages for client mailing,
depending on given AmoCRM entity models.

## Amo model supported
- LeadModel
- ContactModel

## How to use
### Localization
Before use set necessary localization (at now supported only en and ru).
```php
LocaleService::setLocale('ru');
```
### Basic usage
```php
$this->typeFactory = new CommonTypeFactory(); // creates inner representation of AmoCRM custom fields

$this->handlerFactory = new HandlerFactory($this->logger); // creates handler decides placeholder of given custom field looks like
$this->placeholderFactory = new PlaceholderFactory(); // creates placeholder of given custom field representing entity (like 'Lead') and name (like 'Name of lead')

$this->placeholderBuilder = new CommonPlaceholderBuilder($this->logger, $this->handlerFactory, $this->placeholderFactory); // creates an array of placeholders strings of given entity type

$this->customFieldMapper = new CustomFieldMapper($this->logger); // creates an inner representation of AmoCRM custom fields from Amocrm custom field model
$this->entityMapperFactory = new CommonEntityMapperFactory( $this->typeFactory, $this->logger); // creates an inner representation of AmoCRM entity from Amocrm entity model
$this->replacer = new CommonReplacer($this->logger, $this->handlerFactory, $this->placeholderFactory); // creates an array with placeholders values as key and model values as values

/**
 * parses given string template with placeholders and substitutes given model values
 */
$this->templateProcessor = new MessageTemplateProcessorService( 
    $this->logger,
    $this->placeholderBuilder,
    $this->customFieldMapper,
    $this->entityMapperFactory,
    $this->replacer
);

$contactCustomFields = $amocrmClient->customFields(EntityTypesInterface::CONTACTS)->get();

$placeholders = $this->templateProcessor->getVariables(EntityTypeEnum::CONTACT, $contactCustomFields) // managers may choose placeholders from list during client message creation

// then

$contact = new ContactModel();
$contact->setName('John');

$template = 'Hello, {{Contact / Name of contact}}!';

$message = $this->templateProcessor->replaceVariables($template, $contact); // results in Hello, John!
```

### Explanations
1. If there is no value for given placeholder, it will be replaced with empty string.
2. Placeholders pattern consists of entity type and field name divided by '/' like {{Lead / Custom field name}}, so it may result in string of Lead entity even if there is no custom field named 'Custom field name'
   if ```getVariables``` method is used with type Lead but custom fields of Contact entity.
