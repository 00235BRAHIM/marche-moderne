<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // SUPER ADMIN
        // =========================================================

        User::updateOrCreate(
            ['email' => 'admin@marchemodern.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => 'super_admin',
            ]
        );

        // =========================================================
        // ADMIN
        // =========================================================

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // =========================================================
        // CATÉGORIES
        // =========================================================

        $categories = [
            'Mode & Accessoires',
            'Téléphones & Tablettes',
            'Informatique',
            'Électronique',
            'Maison & Décoration',
            'Beauté & Santé',
            'Sport & Loisirs',
            'Automobile',
            'Alimentation',
            'Services',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // =========================================================
        // PLANS D'ABONNEMENT
        // =========================================================

        $plans = [
            [
                'name' => 'Essentiel',
                'description' => 'Pour les vendeurs qui débutent sur Marche Moderne 235.',
                'price' => 15000,
                'duration_days' => 30,
                'product_limit' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'description' => 'Pour les vendeurs qui souhaitent développer leur activité.',
                'price' => 30000,
                'duration_days' => 30,
                'product_limit' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'Pour les vendeurs professionnels avec un catalogue illimité.',
                'price' => 75000,
                'duration_days' => 90,
                'product_limit' => null,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        // =========================================================
        // VENDEURS / BOUTIQUES
        // =========================================================

        $vendor1 = User::updateOrCreate(
            ['email' => 'adam@gmail.com'],
            [
                'name' => 'Adam Moussa',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_status' => 'approved',
                'is_certified' => true,
                'shop_name' => 'Boutique Souk Kabir',
                'shop_slug' => 'boutiquesoukkabir',
            ]
        );

        $vendor2 = User::updateOrCreate(
            ['email' => 'ibrahim@gmail.com'],
            [
                'name' => 'Ibrahim Bichara Ibrahim',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_status' => 'approved',
                'is_certified' => true,
                'shop_name' => 'Boutique Ibra Store',
                'shop_slug' => 'boutiqueibrastore',
            ]
        );

        $vendor3 = User::updateOrCreate(
            ['email' => 'moussa@gmail.com'],
            [
                'name' => 'Moussa Ali',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_status' => 'approved',
                'is_certified' => true,
                'shop_name' => 'Boutique Bella Shop',
                'shop_slug' => 'boutiquebellashop',
            ]
        );

        $vendor4 = User::updateOrCreate(
            ['email' => 'ousman@gmail.com'],
            [
                'name' => 'Ousman Ahmat',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_status' => 'approved',
                'is_certified' => true,
                'shop_name' => 'Boutique Tech Market',
                'shop_slug' => 'boutiquetechmarket',
            ]
        );

        $vendor5 = User::updateOrCreate(
            ['email' => 'zara@gmail.com'],
            [
                'name' => 'Zara Ousmane Ali',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_status' => 'approved',
                'is_certified' => true,
                'shop_name' => 'Boutique Tawali',
                'shop_slug' => 'boutiquetawali',
            ]
        );

        // =========================================================
        // CLIENT
        // =========================================================

        User::updateOrCreate(
            ['email' => 'abdelaziz@gmail.com'],
            [
                'name' => 'Abdelaziz Adam',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // =========================================================
        // CATÉGORIES PRODUITS
        // =========================================================

        $mode = Category::where('slug', 'mode-accessoires')->first();
        $maison = Category::where('slug', 'maison-decoration')->first();

        // =========================================================
        // PRODUIT 1 - SAC À MAIN
        // =========================================================

        Product::updateOrCreate(
            ['sku' => 'SK-SAC-001'],
            [
                'category_id' => $mode?->id,
                'vendor_id' => $vendor1->id,
                'name' => '👜 Sac à main',
                'slug' => 'sac-a-main',
                'description' => 'Sac à main élégant et moderne.',
                'price' => 30000,
                'compare_price' => null,
                'stock' => 5,
                'image' => null,
                'is_active' => true,
                'featured' => false,
                'is_archived' => false,
                'archived_at' => null,
                'was_active_before_subscription_expiry' => true,
            ]
        );

        // =========================================================
        // PRODUIT 2 - LUNETTES
        // =========================================================

        Product::updateOrCreate(
            ['sku' => 'SK-LUN-001'],
            [
                'category_id' => $mode?->id,
                'vendor_id' => $vendor1->id,
                'name' => '🕶️ Lunettes',
                'slug' => 'lunettes',
                'description' => 'Lunettes modernes et élégantes.',
                'price' => 10000,
                'compare_price' => null,
                'stock' => 30,
                'image' => null,
                'is_active' => true,
                'featured' => false,
                'is_archived' => false,
                'archived_at' => null,
                'was_active_before_subscription_expiry' => true,
            ]
        );

        // =========================================================
        // PRODUIT 3 - CHAUSSURES
        // =========================================================

        Product::updateOrCreate(
            ['sku' => 'SK-CHA-001'],
            [
                'category_id' => $mode?->id,
                'vendor_id' => $vendor1->id,
                'name' => '👟 Chaussures',
                'slug' => 'chaussures',
                'description' => 'Chaussures confortables et modernes.',
                'price' => 20000,
                'compare_price' => null,
                'stock' => 10,
                'image' => null,
                'is_active' => true,
                'featured' => false,
                'is_archived' => false,
                'archived_at' => null,
                'was_active_before_subscription_expiry' => true,
            ]
        );

        // =========================================================
        // PRODUIT 4 - CHAISE
        // =========================================================

        Product::updateOrCreate(
            ['sku' => 'SK-CHAISE-001'],
            [
                'category_id' => $maison?->id,
                'vendor_id' => $vendor1->id,
                'name' => '🪑 Chaise',
                'slug' => 'chaise',
                'description' => 'Chaise moderne et confortable.',
                'price' => 55000,
                'compare_price' => null,
                'stock' => 44,
                'image' => null,
                'is_active' => true,
                'featured' => false,
                'is_archived' => false,
                'archived_at' => null,
                'was_active_before_subscription_expiry' => true,
            ]
        );

        // =========================================================
        // MESSAGE FINAL
        // =========================================================

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('     MARCHE MODERNE - SEEDER TERMINE');
        $this->command->info('========================================');
        $this->command->info('Utilisateurs : 8');
        $this->command->info('Vendeurs     : 5');
        $this->command->info('Clients      : 1');
        $this->command->info('Produits     : 4');
        $this->command->info('Catégories   : 10');
        $this->command->info('Plans        : 3');
        $this->command->info('========================================');
        $this->command->info('');
    }
}