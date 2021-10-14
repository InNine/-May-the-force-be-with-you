<?php

declare(strict_types=1);

namespace Model\Enums;

use System\Emerald\Emerald_enum;

class Transaction_type extends Emerald_enum
{
    const ACTION_DEPOSIT = 'deposit';
    const ACTION_BUY = 'buy';
    const ACTION_REFUND = 'refund';

    const OBJECT_WALLET = 'add money to wallet';
    const OBJECT_BOOSTER_PACK = 'buy booster_pack';
}