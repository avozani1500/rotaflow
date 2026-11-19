<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Address;
use App\Repositories\AddressRepository;

final class AddressController extends Controller
{
    private AddressRepository $repository;

    public function __construct()
    {
        $this->repository = new AddressRepository(db());
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $edit = isset($_GET['edit']) ? $this->repository->find((int) $_GET['edit']) : null;
        render('addresses/index', ['addresses' => $this->repository->all($search), 'edit' => $edit, 'search' => $search]);
    }

    public function save(): void
    {
        verify_csrf();
        $data = [
            'identificacao' => trim((string) input('identificacao')),
            'cep' => preg_replace('/\D/', '', (string) input('cep')),
            'logradouro' => trim((string) input('logradouro')),
            'numero' => trim((string) input('numero')),
            'complemento' => trim((string) input('complemento')),
            'bairro' => trim((string) input('bairro')),
            'cidade' => trim((string) input('cidade')),
            'estado' => strtoupper(trim((string) input('estado'))),
        ];
        $errors = Address::validate($data);
        if ($errors) {
            set_old($_POST);
            flash('error', implode(' ', $errors));
            redirect('addresses', $this->editQuery());
        }

        try {
            $this->repository->save($data, (int) input('id') ?: null);
            clear_old();
            flash('success', 'Endereço salvo com sucesso.');
        } catch (\Throwable $exception) {
            $this->persistError($exception, 'Não foi possível salvar o endereço.', 'addresses', $this->editQuery());
        }
        redirect('addresses');
    }

    public function delete(): void
    {
        verify_csrf();
        try {
            $this->repository->delete((int) input('id'));
            flash('success', 'Endereço excluído com sucesso.');
        } catch (\Throwable $exception) {
            $this->persistError($exception, 'Não foi possível excluir o endereço.', 'addresses');
        }
        redirect('addresses');
    }

    private function editQuery(): array
    {
        $id = (int) input('id');
        return $id > 0 ? ['edit' => $id] : [];
    }
}

