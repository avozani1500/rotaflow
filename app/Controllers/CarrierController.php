<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Carrier;
use App\Repositories\CarrierRepository;

final class CarrierController extends Controller
{
    private CarrierRepository $repository;

    public function __construct()
    {
        $this->repository = new CarrierRepository(db());
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $edit = isset($_GET['edit']) ? $this->repository->find((int) $_GET['edit']) : null;
        render('carriers/index', ['carriers' => $this->repository->all($search), 'edit' => $edit, 'search' => $search]);
    }

    public function save(): void
    {
        verify_csrf();
        $data = [
            'nome' => trim((string) input('nome')),
            'documento' => trim((string) input('documento')),
            'telefone' => trim((string) input('telefone')),
            'email' => trim((string) input('email')),
            'placa_veiculo' => strtoupper(trim((string) input('placa_veiculo'))),
        ];
        $errors = Carrier::validate($data);
        if ($errors) {
            set_old($_POST);
            flash('error', implode(' ', $errors));
            redirect('carriers', $this->editQuery());
        }

        try {
            $this->repository->save($data, (int) input('id') ?: null);
            clear_old();
            flash('success', 'Transportador salvo com sucesso.');
        } catch (\Throwable $exception) {
            $this->persistError($exception, 'Não foi possível salvar o transportador.', 'carriers', $this->editQuery());
        }
        redirect('carriers');
    }

    public function delete(): void
    {
        verify_csrf();
        try {
            $this->repository->delete((int) input('id'));
            flash('success', 'Transportador excluído com sucesso.');
        } catch (\Throwable $exception) {
            $this->persistError($exception, 'Não foi possível excluir o transportador.', 'carriers');
        }
        redirect('carriers');
    }

    private function editQuery(): array
    {
        $id = (int) input('id');
        return $id > 0 ? ['edit' => $id] : [];
    }
}

