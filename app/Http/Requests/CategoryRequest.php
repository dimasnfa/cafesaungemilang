<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan pengguna diizinkan untuk membuat kategori
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|unique:kategori,nama_kategori',
        ];
    }
}
