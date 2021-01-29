<?php

use Illuminate\Database\Seeder;
use App\Tag;
use Faker\Generator as Faker;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i=0; $i < 5; $i++) {

            $new_tag = new Tag();
            $new_tag->name = $faker->words(3, true);
            // genero lo slug
            $slug = Str::slug($new_tag->name);
            $slug_base = $slug;
            // verifico che lo slug non esista nel database
            $tag_presente = Tag::where('slug', $slug)->first();
            $contatore = 1;
            // entro nel ciclo while se ho trovato un post con lo stesso $slug
            while($tag_presente) {
                // genero un nuovo slug aggiungendo il contatore alla fine
                $slug = $slug_base . '-' . $contatore;
                $contatore++;
                $tag_presente = Tag::where('slug', $slug)->first();
            }
            // quando esco dal while sono sicuro che lo slug non esiste nel db
            // assegno lo slug al post
            $new_tag->slug = $slug;
            $new_tag->save();
        }
    }
}
