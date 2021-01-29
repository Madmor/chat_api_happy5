<h1 align="center">TESTING BACKEND - HAPPY5 👋</h1>

## 🚀 Getting started
* Clone repository ini.
```markdown
git clone https://github.com/Madmor/chat_api_happy5.git
```
* Copy file .env.example ke .env, lalu sesuiakan bagian DB pada file .env dengan settingan database anda
```markdown
DB_DATABASE={nama_database}
DB_USERNAME={database_user}
DB_PASSWORD={password_database}
```
* Setelah itu ikuti langkah pada Build Setup

## ⚙️ Build Setup

Install depedencies
```sh
composer install
```

Run migration and seeder
```sh
php artisan migrate --seed
```

Run project
```sh
php -S localhost:8000 -t public
```

Setelah selesai, sistem akan berjalan pada url http://localhost:8000.

## User From Seeder
* email : madmor@gmail.com, password : password
* email : bone@gmail.com, password : password


## Tools
* PHP 7.4
* Lumen
* MySQL

## ✨ API DOCUMENTATION
Dokumentasi dari api dapat dilihat pada https://docs.google.com/document/d/19oCL-cCV_C4_zxDW41RaeLtEtkPUAtPNng7mOmU4ME0/edit?usp=sharing

## Happy Code :trollface: (NOT DONE YET)
