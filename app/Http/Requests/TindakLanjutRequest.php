<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TindakLanjutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'arahan_id'      => 'required',
            'unit_kerja_id'  => 'required',
            'periode_bulan'  => 'required|integer|between:1,12',
            'periode_tahun'  => 'required|integer',
            'tindak_lanjut'  => 'required|string',
            'kendala'        => 'nullable|string',
            'keterangan'     => 'nullable|string',
            'evidence'       => 'nullable|file|mimes:pdf,jpg,png,docx|max:5120',

        ];
    }

    public function messages(): array
    {
        return [
            'arahan_id.required'     => 'Pilih arahan yang akan ditindaklanjuti.',
            'unit_kerja_id.required' => 'Unit kerja tidak terdeteksi.',
            'tindak_lanjut.required' => 'Penjelasan tindak lanjut wajib diisi.',
            'tindak_lanjut.min'      => 'Penjelasan minimal 10 karakter.',
            'evidence.mimes'         => 'Bukti harus berupa format: pdf, jpg, png, atau docx.',
            'evidence.max'           => 'Ukuran bukti tidak boleh lebih dari 5MB.',
        ];
    }
}
