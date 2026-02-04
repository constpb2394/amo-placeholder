<?php

declare(strict_types=1);

namespace Constpb\AmoPlaceholder\Entity\Enum;

enum PhoneFieldEnum: string
{
    case AMOCRM_PHONE_WORK = 'WORK';
    case AMOCRM_PHONE_WORKDD = 'WORKDD';
    case AMOCRM_PHONE_MOBILE = 'MOB';
    case AMOCRM_PHONE_HOME = 'HOME';
    case AMOCRM_PHONE_OTHER = 'OTHER';
}
