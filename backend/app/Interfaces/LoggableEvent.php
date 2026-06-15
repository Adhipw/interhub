<?php

namespace App\Interfaces;

interface LoggableEvent
{
    public function getLogAction(): string;

    public function getLogDescription(): string;

    public function getLogSubject();

    public function getLogProperties(): array;

    public function isSensitive(): bool;

    public function isSecurityRisk(): bool;
}
