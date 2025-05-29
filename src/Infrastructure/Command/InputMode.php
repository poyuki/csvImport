<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

enum InputMode: string
{
    case FILE = 'file';
    case STRING = 'string';
    case URL = 'url';
}
