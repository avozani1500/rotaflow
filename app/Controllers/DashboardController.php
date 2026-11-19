<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AddressRepository;
use App\Repositories\CarrierRepository;
use App\Repositories\FreightRepository;
use App\Repositories\ProductRepository;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $products = new ProductRepository(db());
        $addresses = new AddressRepository(db());
        $carriers = new CarrierRepository(db());
        $freights = new FreightRepository(db());
        render('dashboard/index', [
            'counts' => [
                'freights' => $freights->count(),
                'products' => $products->count(),
                'carriers' => $carriers->count(),
                'addresses' => $addresses->count(),
            ],
            'recentFreights' => $freights->recent(5),
        ]);
    }
}

