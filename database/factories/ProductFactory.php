<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $category = \App\Models\Category::query()->where('is_directory', false)->inRandomOrder()->first();

        return [
            'title'        => $this->faker->word,
            'description'  => $this->faker->sentence,
            'image'        => "images/2021/03/12/55e050fca12289d382876509ec709de0.jpg",
            'on_sale'      => true,
            'rating'       => $this->faker->numberBetween(0, 5),
            'sold_count'   => 0,
            'review_count' => 0,
            'price'        => 0,
            'category_id'  => $category ? $category->id : null,
        ];
    }
}
