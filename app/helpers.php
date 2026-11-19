<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $route = 'dashboard', array $query = []): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/rotaflow/public/index.php');
    $base = rtrim(str_replace('/index.php', '', $script), '/');
    $params = array_merge(['route' => $route], $query);

    return $base . '/index.php?' . http_build_query($params);
}

function asset(string $path): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/rotaflow/public/index.php');
    $base = rtrim(str_replace('/index.php', '', $script), '/');

    return $base . '/assets/' . ltrim($path, '/');
}

function redirect(string $route = 'dashboard', array $query = []): never
{
    header('Location: ' . url($route, $query));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = (string) ($_POST['_csrf'] ?? '');
    if (!hash_equals((string) ($_SESSION['_csrf'] ?? ''), $token)) {
        http_response_code(419);
        exit('Token de segurança inválido. Atualize a página e tente novamente.');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $value;
}

function old(string $key, mixed $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    if (!isset($freightCount) || $freightCount === null) {
        try {
            $freightCount = (int) db()->query('SELECT COUNT(*) FROM frete')->fetchColumn();
        } catch (Throwable) {
            $freightCount = null;
        }
    }
    $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

    if (!is_file($viewFile)) {
        throw new RuntimeException('View não encontrada: ' . $view);
    }

    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    require BASE_PATH . '/app/Views/layouts/app.php';
}

function money(float|int|string|null $value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function date_br(?string $date): string
{
    if (!$date) {
        return '';
    }

    return (new DateTimeImmutable($date))->format('d/m/Y');
}

function date_long_br(): string
{
    $days = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
    $months = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    $now = new DateTimeImmutable('today');

    return mb_strtoupper($days[(int) $now->format('w')] . ', ' . $now->format('d') . ' DE ' . $months[(int) $now->format('n')] . ' DE ' . $now->format('Y'), 'UTF-8');
}

function normalize_decimal(mixed $value): float
{
    $value = trim((string) $value);
    if (str_contains($value, ',') && str_contains($value, '.')) {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
    } elseif (str_contains($value, ',')) {
        $value = str_replace(',', '.', $value);
    }
    return (float) $value;
}

function input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function current_route(): string
{
    return trim((string) ($_GET['route'] ?? 'dashboard'), '/');
}

function active_route(string $route): string
{
    return current_route() === $route || str_starts_with(current_route(), $route . '/') ? 'active' : '';
}

