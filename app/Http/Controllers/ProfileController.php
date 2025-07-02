<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * ProfileController
 * Controller untuk mengelola profil user yang sedang login
 * Menyediakan fitur edit profil dan hapus akun
 */
class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil user
     * 
     * @param Request $request - Request object yang berisi data user login
     * @return View - Mengembalikan view form edit profil
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),  // Mengirim data user yang sedang login ke view
        ]);
    }

    /**
     * Memperbarui informasi profil user
     * 
     * @param ProfileUpdateRequest $request - Custom request dengan validasi khusus profil
     * @return RedirectResponse - Redirect kembali ke halaman edit dengan status success
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Mengisi data user dengan data yang sudah divalidasi
        $request->user()->fill($request->validated());

        // Jika email berubah, reset status verifikasi email
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;  // Reset verifikasi email jika email berubah
        }

        // Simpan perubahan ke database
        $request->user()->save();

        // Redirect kembali ke form edit dengan pesan sukses
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun user (soft/hard delete)
     * 
     * @param Request $request - Request yang berisi konfirmasi password
     * @return RedirectResponse - Redirect ke halaman utama setelah akun dihapus
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password saat ini untuk konfirmasi penghapusan akun
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],  // Password harus benar untuk konfirmasi
        ]);

        $user = $request->user();  // Ambil data user yang akan dihapus

        Auth::logout();  // Logout user sebelum menghapus akun

        $user->delete();  // Hapus data user dari database

        // Invalidasi session untuk keamanan
        $request->session()->invalidate();      // Hapus semua data session
        $request->session()->regenerateToken(); // Generate token CSRF baru

        // Redirect ke halaman utama setelah akun dihapus
        return Redirect::to('/');
    }
}
