<?php
namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Services\MovieService;

class MovieController extends Controller
{
    // Menampilkan list movie dengan search
    public function index()
    {
        $query = Movie::latest();
        if (request('search')) {
            $query->where('judul', 'like', '%' . request('search') . '%')
                ->orWhere('sinopsis', 'like', '%' . request('search') . '%');
        }
        $movies = $query->paginate(6)->withQueryString();
        return view('homepage', compact('movies'));
    }

    // Menampilkan detail movie
    public function detail($id)
    {
        $movie = Movie::find($id);
        return view('detail', compact('movie'));
    }

    // Menampilkan form input movie
    public function create()
    {
        $categories = Category::all();
        return view('input', compact('categories'));
    }

    // Menyimpan movie baru
    public function store(StoreMovieRequest $request, MovieService $service)
    {
        // Ambil data dari request yang sudah tervalidasi
        $data = $request->validated();

        // Upload foto sampul
        $data['foto_sampul'] = $service->handleUploadFoto($request->file('foto_sampul'));

        // Simpan movie ke database
        $service->create($data);

        return redirect('/')->with('success', 'Data berhasil disimpan');
    }

    // Menampilkan data movie
    public function data()
    {
        $movies = Movie::latest()->paginate(10);
        return view('data-movies', compact('movies'));
    }

    // Menampilkan form edit movie
    public function form_edit($id)
    {
        $movie = Movie::find($id);
        $categories = Category::all();
        return view('form-edit', compact('movie', 'categories'));
    }

    // Mengupdate data movie
    public function update(UpdateMovieRequest $request, $id, MovieService $service)
    {
        // Cari movie yang akan diupdate
        $movie = Movie::findOrFail($id);

        // Ambil data dari request yang sudah tervalidasi
        $data = $request->validated();

        // Jika ada foto yang diupload, hapus foto lama dan upload foto baru
        if ($request->hasFile('foto_sampul')) {
            $service->deleteOldFoto($movie->foto_sampul);
            $data['foto_sampul'] = $service->handleUploadFoto($request->file('foto_sampul'));
        }

        // Update movie di database
        $service->update($movie, $data);

        return redirect('/movies/data')->with('success', 'Data berhasil diperbarui');
    }

    // Menghapus data movie
    public function delete($id, MovieService $service)
    {
        $movie = Movie::findOrFail($id);

        // Hapus foto lama jika ada
        $service->deleteOldFoto($movie->foto_sampul);

        // Hapus movie dari database
        $movie->delete();

        return redirect('/movies/data')->with('success', 'Data berhasil dihapus');
    }
}

