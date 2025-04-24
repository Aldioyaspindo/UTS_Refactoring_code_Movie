<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MovieService
{
    public function handleUploadFoto($file): string
    {
        $randomName = Str::uuid()->toString();
        $ext = $file->getClientOriginalExtension();
        $fileName = $randomName . '.' . $ext;
        $file->move(public_path('images'), $fileName);
        return $fileName;
    }

    public function deleteOldFoto(string $filename): void
    {
        $path = public_path('images/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    public function create(array $data): Movie
    {
        return Movie::create($data);
    }

    public function update(Movie $movie, array $data): void
    {
        $movie->update($data);
    }
}
