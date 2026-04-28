<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Artist;

final class ArtistController extends Controller
{
    public function index(): void
    {
        $artists = (new Artist())->all();

        $this->render('artist/index', [
            'artists' => $artists,
        ]);
    }

    public function create(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $this->render('artist/create');
    }

    public function store(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $description === '') {
            $this->render('artist/create', ['error' => 'All fields are required.']);
            return;
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($name)));

        $imagePath = null;

        if (!empty($_FILES['image']['tmp_name'])) {
            $uploadDir = '/uploads/artists/';
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = $slug . '-' . time() . '.' . $ext;
            $target = PUBLIC_PATH . $uploadDir . $filename;

            if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $this->render('artist/create', ['error' => 'Invalid image type.']);
                return;
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $this->render('artist/create', ['error' => 'Failed to move uploaded file.']);
                return;
            }

            $imagePath = $uploadDir . $filename;
        }

        $model = new Artist();
        $model->db->prepare('INSERT INTO artists (name, slug, description, image_path) VALUES (:name, :slug, :description, :image_path)')
            ->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
                ':image_path' => $imagePath,
            ]);

        redirect('/artists');
    }
}
