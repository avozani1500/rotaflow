<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Repositories\ProductRepository;

final class ProductController extends Controller
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository(db());
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $edit = isset($_GET['edit']) ? $this->repository->find((int) $_GET['edit']) : null;
        render('products/index', ['products' => $this->repository->all($search), 'edit' => $edit, 'search' => $search]);
    }

    public function save(): void
    {
        verify_csrf();
        $data = [
            'nome' => trim((string) input('nome')),
            'descricao' => trim((string) input('descricao')),
            'peso' => normalize_decimal(input('peso')),
            'valor' => normalize_decimal(input('valor')),
        ];
        $errors = Product::validate($data);
        if ($errors) {
            set_old($_POST);
            flash('error', implode(' ', $errors));
            redirect('products');
        }

        try {
            $this->repository->save($data, (int) input('id') ?: null);
            clear_old();
            flash('success', 'Produto salvo com sucesso.');
        } catch (\Throwable $exception) {
            $this->persistError($exception, 'Não foi possível salvar o produto.');
        }
        redirect('products');
    }

    public function delete(): void
    {
        verify_csrf();
        try {
            $this->repository->delete((int) input('id'));
            flash('success', 'Produto excluído com sucesso.');
        } catch (\Throwable $exception) {
            $this->persistError($exception, 'Não foi possível excluir o produto.');
        }
        redirect('products');
    }
}

