<?php

declare(strict_types=1);

namespace App\Shared\Modules;

final class ModuleRegistry
{
    public function modules(): array
    {
        return Module::cases();
    }
}
