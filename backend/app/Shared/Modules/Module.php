<?php

declare(strict_types=1);

namespace App\Shared\Modules;

enum Module: string
{
    case Identity = 'Identity';
    case Wallet = 'Wallet';
    case Payments = 'Payments';
    case Loyalty = 'Loyalty';
    case Affiliate = 'Affiliate';
    case Promotions = 'Promotions';
    case Packages = 'Packages';
    case Notifications = 'Notifications';
    case Chat = 'Chat';
    case Support = 'Support';
    case Ai = 'Ai';
    case Invoicing = 'Invoicing';
    case Accounting = 'Accounting';
    case Inventory = 'Inventory';
    case Pos = 'Pos';
    case Hr = 'Hr';
    case Storefront = 'Storefront';
    case Shipping = 'Shipping';
    case Freelance = 'Freelance';
    case Auctions = 'Auctions';
    case Delivery = 'Delivery';
    case Orders = 'Orders';

    public function namespace(): string
    {
        return 'App\\Modules\\'.$this->value;
    }

    public function provider(): string
    {
        return $this->namespace().'\\'.$this->value.'ServiceProvider';
    }

    public function directory(): string
    {
        return app_path('Modules/'.$this->value);
    }
}
