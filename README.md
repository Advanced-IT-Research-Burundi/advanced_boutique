# Mutilstock - Advanced Boutique POS

Système de gestion multi-stocks avec suivi des ventes, achats, transferts et caisse.

## 🛠️ Stack
- Laravel 10
- Blade + bootstrap
- MySQL
- Auth: Laravel Breeze / Jetstream

## 📁 Structure
- `app/Http/Controllers/` : logiques par modules
- `resources/views/` : dossiers organisés par entité métier
- `routes/web.php` : routes principales
- `database/seeders/` : jeux de données initiales

## 👥 Équipe
- **Jean Lionel** : Fullstack / Leader
- **Bienvenue** : Backend Laravel
- **Chriss** : Backend 
- **Shamah** : Frontend Design
- **Brice** : Blade Integration & Junior Dev

## 🚀 Setup du projet

```bash
git clone https://github.com/Advanced-IT-Research-Burundi/advanced_boutique.git
cd advanced_boutique
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
php artisan serve
