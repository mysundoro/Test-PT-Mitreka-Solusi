### Laravel 11 Inertia Vue SSR

## Fitur
1. Integrasi Vuetfiy
2. Multi Bahasa
3. Multi Role
4. Multi User

## Instalasi Laravel 11 Jetstream (Inertia Vue SSR)
1. New Laravel tes_module
2. cd tes_module
3. php artisan migrate

## Instalasi Spatie Permission
1. composer require spatie/laravel-permission
2. php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
3. php artisan optimize:clear
4. php artisan config:clear
5. php artisan migrate

## Instalasi Vuetify
1. npm uninstall tailwindcss
2. npm uninstall autoprefixer
3. npm uninstall postcss
4. Hapus tailwind.config.js
5. Hapus postcss.config.js
6. npm install vuetify
7. edit resources\js\app.js
    1. import vuetify from "./vuetify";
    2. .use(vuetify)
8. Buat vuetify.js di dalam folder resources\js\

## Instalasi Laravel Audit
1. composer require owen-it/laravel-auditing
2. php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="config"
3. php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="migrations"
4. php artisan migrate

## Finalisasi
1. php artisan server
2. npm run dev

## Instalasi untuk pihak DOKA DIGITAL
1. buat .env baru berdasarkan .env.example dan ubah pengaturan database
2. composer install
3. npm install
4. php artisan migrate (Jika error (Bug spatie package), drop semua table di database (misal tes_db). Kemudian run php artisan migrate kembali)
5. php artisan db:seed
6. php artisan storage:link
7. npm run dev (development)
8. php artisan serve (development)
9. npm run build (production)

## Default login
    email : superadmin@demo.com
    password : password

## NOTE
Untuk bahasa saya tidak sempat untuk menyertakan seedernya karena keterbatasan waktu.
Namun anda dapat menambahkan bahasa di dalam menu setting/language -> Add.
Jika ingin menambahkan bahasa baru? klik tombol Language Code di dalam menu Language.

Notifikasi hanya bisa di gunakan ketika mencreate user baru di dalam menu setting/users.


Update v1.1
1. php artisan migrate