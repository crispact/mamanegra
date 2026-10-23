<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    private const TOP_SLIDER = [
        ['imagen' => '/assets/img/bg-img/mamanegra.jpg',    'title' => 'La Mama Negra',         'slug' => 'la-mama-negra'],
        ['imagen' => '/assets/img/bg-img/capitan.jpg',      'title' => 'El Capitán',             'slug' => 'el-capitan'],
        ['imagen' => '/assets/img/bg-img/abanderado.jpg',   'title' => 'El Abanderado',          'slug' => 'el-abanderado'],
        ['imagen' => '/assets/img/bg-img/angel-estrella.jpg','title' => 'El Angel de la Estrella','slug' => 'el-angel-de-la-estrella'],
        ['imagen' => '/assets/img/bg-img/rey-moro.jpg',     'title' => 'El Rey Moro',            'slug' => 'el-rey-moro'],
    ];

    public function __construct(
        private readonly Twig $twig,
        private readonly PDO  $pdo
    ) {}

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'index.html.twig', [
            'topSlider' => self::TOP_SLIDER,
            'titulo'    => 'inicio - la comparsa de la mama negra',
            'slider'    => $this->sliderPhotos(),
            'otros'     => Post::random($this->pdo, 4),
            'url'       => '',
        ]);
    }

    public function showPost(Request $request, Response $response, array $args): Response
    {
        $slug = $args['slug'] ?? '';
        $post = Post::findBySlug($this->pdo, $slug);

        if ($post === null) {
            return $this->twig->render($response->withStatus(404), '404.html.twig');
        }

        $url = in_array($post['op1'], ['personajes', 'la-comparsa'], true)
            ? $post['op1'] . '/' . $slug
            : $slug;

        return $this->twig->render($response, 'article.html.twig', [
            'titulo' => $post['title'],
            'post'   => $post,
            'slider' => $this->sliderPhotos(),
            'otros'  => Post::random($this->pdo, 5),
            'url'    => $url,
        ]);
    }

    public function latacunga(Request $request, Response $response): Response
    {
        return $this->showPost($request, $response, ['slug' => 'latacunga']);
    }

    public function volcanCotopaxi(Request $request, Response $response): Response
    {
        return $this->showPost($request, $response, ['slug' => 'volcan-cotopaxi']);
    }

    public function atractivos(Request $request, Response $response): Response
    {
        return $this->showPost($request, $response, ['slug' => 'atractivos']);
    }

    public function comparsa(Request $request, Response $response): Response
    {
        return $this->showPost($request, $response, ['slug' => 'la-comparsa-de-la-mama-negra']);
    }

    public function contacto(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'nosotros.html.twig', [
            'titulo' => 'contacto - la comparsa de la mama negra',
            'slider' => $this->sliderPhotos(),
            'otros'  => Post::random($this->pdo, 4),
            'url'    => 'contacto',
        ]);
    }

    public function search(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams()['search'] ?? '';

        return $this->twig->render($response, 'search.html.twig', [
            'resultados' => Post::search($this->pdo, $query),
            'titulo'     => 'busqueda - la comparsa de la mama negra',
            'slider'     => $this->sliderPhotos(),
            'otros'      => Post::random($this->pdo, 5),
            'url'        => 'search',
        ]);
    }

    private function sliderPhotos(): array
    {
        // From app/src/Controllers/ go up 3 levels to project root, then into public_html
        $dir = dirname(__DIR__, 3) . '/public_html/assets/img/slider-img/';

        if (!is_dir($dir)) {
            return [];
        }

        $files = array_values(array_filter(
            scandir($dir),
            static fn(string $f): bool => !in_array($f, ['.', '..'], true) && is_file($dir . $f)
        ));

        $count = count($files);
        if ($count <= 7) {
            return $files;
        }

        $keys = (array) array_rand($files, 7);
        return array_map(static fn(int $k): string => $files[$k], $keys);
    }
}