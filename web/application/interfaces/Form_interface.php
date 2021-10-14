<?php

declare(strict_types=1);

namespace Interfaces;

interface Form_interface
{

    public function fill(array $data): void;

    public function validate(): bool;
}