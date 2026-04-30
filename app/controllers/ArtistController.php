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

            if (!store_uploaded_image($_FILES['image'], $target, 800, 800)) {
                $this->render('artist/create', ['error' => 'Failed to move uploaded file.']);
                return;
            }

            $imagePath = $uploadDir . $filename;
        }

        (new Artist())->create($name, $slug, $description, $imagePath);

        redirect('/artists');
    }

    public function edit(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);
        $artist = (new Artist())->findById($id);

        if ($artist === null) {
            http_response_code(404);
            echo 'Artist not found';
            return;
        }

        $this->render('artist/edit', ['artist' => $artist]);
    }

    public function update(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $artist = (new Artist())->findById($id);

        if ($artist === null) {
            redirect('/artists');
            return;
        }

        $name = trim($_POST['name'] ?? '') ;
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $description === '') {
            $this->render('artist/edit', ['artist' => $artist, 'error' => 'All fields are required.']);
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
                $this->render('artist/edit', ['artist' => $artist, 'error' => 'Invalid image type.']);
                return;
            }

            if (!store_uploaded_image($_FILES['image'], $target, 800, 800)) {
                $this->render('artist/edit', ['artist' => $artist, 'error' => 'Failed to move uploaded file.']);
                return;
            }

            $imagePath = $uploadDir . $filename;
        }

        (new Artist())->update($id, $name, $slug, $description, $imagePath);

        redirect('/artists');
    }

    public function delete(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $artist = (new Artist())->findById($id);

        if ($artist === null) {
            redirect('/artists');
            return;
        }

        if (!empty($artist['image_path'])) {
            $file = PUBLIC_PATH . $artist['image_path'];
            if (is_file($file)) {
                @unlink($file);
            }
        }

        (new Artist())->delete($id);

        redirect('/artists');
    }
}
