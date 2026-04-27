<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keputusan_id'          => 'required|exists:keputusan,id',
            'bidang_id'             => 'required|exists:bidang,id',
            'tanggal_target'        => 'required|date',
            'strategi'              => 'required|string',
            'pic_unit_kerja_ids'    => 'required|array|min:1',
            'pic_unit_kerja_ids.*'  => 'exists:users,id',
            'after_save'            => 'nullable|in:continue,finish'
        ];
    }

    public function messages(): array
    {
        return [
            'keputusan_id.required' => 'Keputusan RUPS wajib dipilih.',
            'bidang_id.required' => 'Bidang wajib dipilih.',
            'pic_unit_kerja_ids.required' => 'Minimal pilih 1 PIC penanggung jawab.',
            'pic_unit_kerja_ids.min' => 'Minimal pilih 1 PIC penanggung jawab.',
            'tanggal_target.required' => 'Tanggal target wajib diisi.',
            'strategi.required' => 'Strategi arahan wajib diisi.',
            'strategi.min' => 'Strategi minimal 10 karakter.',
        ];
    }
}