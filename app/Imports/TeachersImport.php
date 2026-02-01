<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row['nama_lengkap'],
            'email' => $row['email'],
            'password' => Hash::make('guru123'), // Default password
            'role' => 'guru',
            'is_approved' => true,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'email.unique' => 'Email :input sudah terdaftar di sistem.',
            'email.required' => 'Kolom email wajib diisi.',
            'nama_lengkap.required' => 'Kolom nama_lengkap wajib diisi.',
        ];
    }
}
