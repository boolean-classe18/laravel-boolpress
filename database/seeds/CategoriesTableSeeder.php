<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i=0; $i < 5; $i++) {

            $new_category = new Category();
            $new_category->name = $faker->words(3, true);
            // genero lo slug
            $slug = Str::slug($new_category->name);
            $slug_base = $slug;
            // verifico che lo slug non esista nel database
            $categoria_presente = Category::where('slug', $slug)->first();
            $contatore = 1;
            // entro nel ciclo while se ho trovato un post con lo stesso $slug
            while($categoria_presente) {
                // genero un nuovo slug aggiungendo il contatore alla fine
                $slug = $slug_base . '-' . $contatore;
                $contatore++;
                $categoria_presente = Category::where('slug', $slug)->first();
            }
            // quando esco dal while sono sicuro che lo slug non esiste nel db
            // assegno lo slug al post
            $new_category->slug = $slug;
            $new_category->save();
        }
    }
}
