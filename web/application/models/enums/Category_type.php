<?php

declare(strict_types=1);

namespace Model\Enums;

use System\Emerald\Emerald_enum;

class Category_type extends Emerald_enum
{
    const POST = 'post';
    const COMMENT = 'comment';
}
