<?php

namespace App\Contracts;

interface NavigationGroupInterface
{
    public function getNavigationGroup(): ?string;
    public function getNavigationSort(): int;
    public function getNavigationIcon(): ?string;
    public function getNavigationGroupIcon(): ?string;
    public function getNavigationGroupSort(): int;
}
