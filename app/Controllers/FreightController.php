<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Freight;
use App\Repositories\AddressRepository;
use App\Repositories\CarrierRepository;
use App\Repositories\FreightRepository;
use App\Repositories\ProductRepository;
use App\Services\FreightService;

final class FreightController extends Controller
{
    private FreightRepository $freights;
    private ProductRepository $products;
    private AddressRepository $addresses;
    private CarrierRepository $carriers;
    private FreightService $service;

    public function __construct()
    {
        $pdo = db();
        $this->freights = new FreightRepository($pdo);
        $this->products = new ProductRepository($pdo);
        $this->addresses = new AddressRepository($pdo);
        $this->carriers = new CarrierRepository($pdo);
        $this->service = new FreightService($this->freights, $this->products, $this->addresses, $this->carriers);
    }

    public function index(): void
    {
        render('freights/index', [
            'freights' => $this->freights->all($_GET['from'] ?? null, $_GET['to'] ?? null),
            'from' => (string) ($_GET['from'] ?? ''),
            'to' => (string) ($_GET['to'] ?? ''),
        ]);
    }

    public function create(): void
    {
        $edit = isset($_GET['edit']) ? $this->freights->find((int) $_GET['edit']) : null;
        render('freights/form', [
            'freight' => $edit,
            'products' => $this->products->all(),
            'addresses' => $this->addresses->all(),
            'carriers' => $this->carriers->all(),
            'statuses' => Freight::STATUSES,
        ]);
    }

    public function save(): void
    {
        verify_csrf();
        try {
            $id = (int) input('id') ?: null;
            $freightId = $this->service->save($_POST, $id);
            clear_old();
            flash('success', $id ? 'Frete atualizado com sucesso.' : 'Frete criado com sucesso.');
            redirect('freights/show', ['id' => $freightId]);
        } catch (\Throwable $exception) {
            set_old($_POST);
            flash('error', $exception->getMessage() ?: 'Não foi possível salvar o frete.');
            redirect('freights/create', input('id') ? ['edit' => (int) input('id')] : []);
        }
    }

    public function show(): void
    {
        $freight = $this->freights->find((int) ($_GET['id'] ?? 0));
        if (!$freight) {
            flash('error', 'Frete não encontrado.');
            redirect('freights');
        }
        render('freights/show', ['freight' => $freight]);
    }
}

