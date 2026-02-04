<?php

namespace Constpb\AmoPlaceholder\Entity\Enum;

enum EmailFieldEnum: string
{
    case AMOCRM_EMAIL_WORK = 'WORK';
    case AMOCRM_EMAIL_PRIV = 'PRIV';
    case AMOCRM_EMAIL_OTHER = 'OTHER';
}
