<?php

use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    protected $faker;

    /**
     * ProdutoSeeder constructor.
     * @param $faker
     */
    public function __construct(Faker\Generator $faker)
    {
        $this->faker = $faker;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('product')->delete();
        for($x = 0; $x < 50; $x++){
            \App\Entities\Product::create([
                'productName'           => $this->faker->name,
                'productPrice'          => $this->faker->randomFloat(2),
                'productPhoto'          => $this->faker->randomElement(['1.jpg', '2.jpg', '3.jpg']),
                'productDescription'    => $this->faker->text(),
                'productStock'          => $this->faker->numberBetween(0, 100),
                'productSpecial'        => $this->faker->boolean(30)
            ]);
        }
    }
}
