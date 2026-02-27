<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Disposed = 'disposed';
}
