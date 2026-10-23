<?php

declare(strict_types=1);

use Slim\Views\Twig;
use Twig\TwigFunction;

return [

    Twig::class => function (): Twig {
        $twig = Twig::create(__DIR__ . '/../templates', [
            'cache' => false,
            'autoescape' => 'html',
        ]);

        $env = $twig->getEnvironment();

        $env->addFunction(new TwigFunction('share_link', function (
            string $path,
            string $network,
            string $title = '',
            string $image = ''
        ): string {
            $base = 'https://www.mamanegra.com.ec';
            $url  = $base . $path;
            return match ($network) {
                'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
                'twitter'   => 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title),
                'pinterest' => 'http://pinterest.com/pin/create/button/?url=' . urlencode($url)
                                . '&media=' . urlencode($base . $image)
                                . '&description=' . urlencode($title),
                default     => '#',
            };
        }));

        return $twig;
    },

    PDO::class => function (): PDO {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST']     ?? '127.0.0.1',
            $_ENV['DB_PORT']     ?? '3306',
            $_ENV['DB_DATABASE'] ?? ''
        );
        $pdo = new PDO($dsn, $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    },

];