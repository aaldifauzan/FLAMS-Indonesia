<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(3)->create();

        // User::create([
        //     'name' => 'Aldi Fauzan',
        //     'username' => 'aldifauzaan',
        //     'email' => 'aldifauzan2047@gmail.com',
        //     'password' => bcrypt('password')
        // ]);
        // User::create([
        //     'name' => 'Testing',
        //     'email' => 'yesst@gmail.com',
        //     'password' => bcrypt('1122345')
        // ]);
        Category::create([
            'name' => 'Bali',
            'slug' => 'bali'
        ]);
        Category::create([
            'name' => 'Banten',
            'slug' => 'banten'
        ]);
        Category::create([
            'name' => 'Bengkulu',
            'slug' => 'bengkulu'
        ]);

        Category::create([
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta'
        ]);

        Category::create([
            'name' => 'DKI Jakarta',
            'slug' => 'dki-jakarta'
        ]);

        Category::create([
            'name' => 'Gorontalo',
            'slug' => 'gorontalo'
        ]);

        Category::create([
            'name' => 'Jambi',
            'slug' => 'jambi'
        ]);

        Category::create([
            'name' => 'Jawa Barat',
            'slug' => 'jawa-barat'
        ]);

        Category::create([
            'name' => 'Jawa Tengah',
            'slug' => 'jawa-tengah'
        ]);

        Category::create([
            'name' => 'Jawa Timur',
            'slug' => 'jawa-timur'
        ]);

        Category::create([
            'name' => 'Kalimantan Barat',
            'slug' => 'kalimantan-barat'
        ]);

        Category::create([
            'name' => 'Kalimantan Selatan',
            'slug' => 'kalimantan-selatan'
        ]);

        Category::create([
            'name' => 'Kalimantan Tengah',
            'slug' => 'kalimantan-tengah'
        ]);

        Category::create([
            'name' => 'Kalimantan Timur',
            'slug' => 'kalimantan-timur'
        ]);

        Category::create([
            'name' => 'Kalimantan Utara',
            'slug' => 'kalimantan-utara'
        ]);

        Category::create([
            'name' => 'Kep. Bangka Belitung',
            'slug' => 'kep-bangka-belitung'
        ]);

        Category::create([
            'name' => 'Kep. Riau',
            'slug' => 'kep-riau'
        ]);

        Category::create([
            'name' => 'Lampung',
            'slug' => 'lampung'
        ]);

        Category::create([
            'name' => 'Maluku',
            'slug' => 'maluku'
        ]);

        Category::create([
            'name' => 'Maluku Utara',
            'slug' => 'maluku-utara'
        ]);

        Category::create([
            'name' => 'Nanggroe Aceh Darussalam',
            'slug' => 'nanggroe-aceh-darussalam'
        ]);

        Category::create([
            'name' => 'Nusa Tenggara Barat',
            'slug' => 'nusa-tenggara-barat'
        ]);

        Category::create([
            'name' => 'Nusa Tenggara Timur',
            'slug' => 'nusa-tenggara-timur'
        ]);

        Category::create([
            'name' => 'Papua',
            'slug' => 'papua'
        ]);

        Category::create([
            'name' => 'Papua Barat',
            'slug' => 'papua-barat'
        ]);

        Category::create([
            'name' => 'Riau',
            'slug' => 'riau'
        ]);

        Category::create([
            'name' => 'Sulawesi Barat',
            'slug' => 'sulawesi-barat'
        ]);
        
        Category::create([
            'name' => 'Sulawesi Selatan',
            'slug' => 'sulawesi-selatan'
        ]);

        Category::create([
            'name' => 'Sulawesi Tengah',
            'slug' => 'sulawesi-tengah'
        ]);

        Category::create([
            'name' => 'Sulawesi Tenggara',
            'slug' => 'sulawesi-tenggara'
        ]);

        Category::create([
            'name' => 'Sulawesi Utara',
            'slug' => 'sulawesi-utara'
        ]);

        Category::create([
            'name' => 'Sumatera Barat',
            'slug' => 'sumatera-barat'
        ]);

        Category::create([
            'name' => 'Sumatera Selatan',
            'slug' => 'sumatera-selatan'
        ]);

        Category::create([
            'name' => 'Sumatera Utara',
            'slug' => 'sumatera-utara'
        ]);
        // \App\Models\Post::factory(20)->create();

        // Post::create([
        //     'title' => 'Judul Pertama',
        //     'slug' => 'judul-pertama',
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque,',
        //     'body' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque, commodi architecto cupiditate similique quo alias tenetur facere officia? Laudantium nostrum similique voluptate aliquid maiores officiis deleniti maxime odit voluptas? Dicta, dignissimos et, sit id eum nisi minus deleniti quaerat accusantium quam ullam atque quia iure. Impedit placeat neque tenetur esse quisquam quibusdam aliquid? Iste modi architecto dignissimos omnis voluptate commodi sunt assumenda iure? Vitae ab commodi provident natus, assumenda deleniti explicabo quidem at omnis debitis asperiores voluptatibus eveniet. Incidunt expedita distinctio quibusdam! Expedita distinctio, ut vel quia soluta natus, alias eius animi labore officiis esse iste asperiores? Libero quisquam minima reprehenderit vel accusantium aut hic id nam, temporibus molestiae illum fugiat sequi.',
        //     'category_id' => 1,
        //     'user_id' => 1
        // ]);
        // Post::create([
        //     'title' => 'Judul Kedua',
        //     'slug' => 'judul-kedua',
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque,',
        //     'body' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque, commodi architecto cupiditate similique quo alias tenetur facere officia? Laudantium nostrum similique voluptate aliquid maiores officiis deleniti maxime odit voluptas? Dicta, dignissimos et, sit id eum nisi minus deleniti quaerat accusantium quam ullam atque quia iure. Impedit placeat neque tenetur esse quisquam quibusdam aliquid? Iste modi architecto dignissimos omnis voluptate commodi sunt assumenda iure? Vitae ab commodi provident natus, assumenda deleniti explicabo quidem at omnis debitis asperiores voluptatibus eveniet. Incidunt expedita distinctio quibusdam! Expedita distinctio, ut vel quia soluta natus, alias eius animi labore officiis esse iste asperiores? Libero quisquam minima reprehenderit vel accusantium aut hic id nam, temporibus molestiae illum fugiat sequi.',
        //     'category_id' => 1,
        //     'user_id' => 1
        // ]);
        // Post::create([
        //     'title' => 'Judul Ketiga',
        //     'slug' => 'judul-ketiga',
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque,',
        //     'body' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque, commodi architecto cupiditate similique quo alias tenetur facere officia? Laudantium nostrum similique voluptate aliquid maiores officiis deleniti maxime odit voluptas? Dicta, dignissimos et, sit id eum nisi minus deleniti quaerat accusantium quam ullam atque quia iure. Impedit placeat neque tenetur esse quisquam quibusdam aliquid? Iste modi architecto dignissimos omnis voluptate commodi sunt assumenda iure? Vitae ab commodi provident natus, assumenda deleniti explicabo quidem at omnis debitis asperiores voluptatibus eveniet. Incidunt expedita distinctio quibusdam! Expedita distinctio, ut vel quia soluta natus, alias eius animi labore officiis esse iste asperiores? Libero quisquam minima reprehenderit vel accusantium aut hic id nam, temporibus molestiae illum fugiat sequi.',
        //     'category_id' => 2,
        //     'user_id' => 1
        // ]);
        // Post::create([
        //     'title' => 'Judul Keempat',
        //     'slug' => 'judul-keempat',
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque,',
        //     'body' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum, rem veniam perspiciatis nostrum ratione exercitationem qui doloremque, commodi architecto cupiditate similique quo alias tenetur facere officia? Laudantium nostrum similique voluptate aliquid maiores officiis deleniti maxime odit voluptas? Dicta, dignissimos et, sit id eum nisi minus deleniti quaerat accusantium quam ullam atque quia iure. Impedit placeat neque tenetur esse quisquam quibusdam aliquid? Iste modi architecto dignissimos omnis voluptate commodi sunt assumenda iure? Vitae ab commodi provident natus, assumenda deleniti explicabo quidem at omnis debitis asperiores voluptatibus eveniet. Incidunt expedita distinctio quibusdam! Expedita distinctio, ut vel quia soluta natus, alias eius animi labore officiis esse iste asperiores? Libero quisquam minima reprehenderit vel accusantium aut hic id nam, temporibus molestiae illum fugiat sequi.',
        //     'category_id' => 2,
        //     'user_id' => 2
        // ]);
    }
}
