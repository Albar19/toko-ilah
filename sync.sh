#!/bin/bash

# Jangan masukan komentar dalam kode



PROJECT_ROOT_DIR="/home/ilah/toko-ilah"

WEB_USER="www-data"    

PROJECT_OWNER="www-data" 



if [[ $EUID -ne 0 ]]; then

   echo "Script ini harus dijalankan sebagai root atau dengan sudo."

   exit 1

fi



if [ ! -d "$PROJECT_ROOT_DIR" ]; then

    echo "Error: Direktori proyek '$PROJECT_ROOT_DIR' tidak ditemukan."

    exit 1

fi



echo "--- Mengatur Izin Direktori Induk agar Nginx dapat Mengakses ---"



# 1. Pastikan direktori home user pemilik proyek memiliki izin eksekusi (dan baca) untuk others

# Ini penting agar www-data bisa masuk ke direktori home user pemilik proyek

echo "Mengatur izin untuk direktori home user '$USER'..."

# Mendapatkan path direktori home dari PROJECT_ROOT_DIR (misal: /home/rizal)

HOME_DIR=$(dirname "$PROJECT_ROOT_DIR")

sudo chmod o+rx "$HOME_DIR"



# 2. Pastikan direktori root proyek itu sendiri memiliki izin yang sama

echo "Mengatur izin untuk $PROJECT_ROOT_DIR..."

sudo chmod o+rx "$PROJECT_ROOT_DIR"



echo "--- Menjalankan kembali pengaturan izin untuk proyek Laravel ---"



cd "$PROJECT_ROOT_DIR" || { echo "Gagal masuk ke direktori proyek."; exit 1; }



echo "Mengatur kepemilikan direktori 'storage' dan 'bootstrap/cache' ke $PROJECT_OWNER:$WEB_USER..."

sudo chown -R "$PROJECT_OWNER":"$WEB_USER" storage bootstrap/cache



echo "Memberikan izin tulis untuk user dan grup pada 'storage' dan 'bootstrap/cache' (ug+rwx), dan baca/eksekusi untuk 'others' (775/777)..."

sudo chmod -R ug+rwx storage bootstrap/cache

sudo chmod -R o+rx storage bootstrap/cache



echo "Mengatur setgid bit pada 'storage' dan 'bootstrap/cache' (g+s)..."

sudo chmod -R g+s storage bootstrap/cache



echo "Mengatur izin default untuk file (664) dan direktori (775) lainnya pada seluruh proyek..."

sudo find . -type f -exec chmod 664 {} \;

sudo find . -type d -exec chmod 775 {} \;



echo "Memastikan file .env ada..."

if [ ! -f ".env" ]; then

    echo "File .env tidak ditemukan. Menyalin dari .env.example..."

    cp .env.example .env

    echo "PENTING: Harap edit file .env untuk mengkonfigurasi database, APP_KEY, dll.!"

fi



echo "Menghasilkan APP_KEY..."

php artisan key:generate



echo "Membersihkan cache Laravel..."

php artisan cache:clear

php artisan config:clear

php artisan route:clear

php artisan view:clear



echo "Pengaturan permission untuk deployment Laravel selesai."

echo "Pastikan konfigurasi web server (Nginx/Apache) sudah menunjuk ke '$PROJECT_ROOT_DIR/public'."