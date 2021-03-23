<?php

use App\Models\CrowdfundingProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrowdfundingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crowdfunding_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->decimal('target_amount', 10, 4);
            $table->decimal('total_amount', 10, 4)->default(0);
            $table->unsignedInteger('user_count')->default(0);
            $table->dateTime('end_at');
            $table->string('status')->default(CrowdfundingProduct::STATUS_FUNDING);
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crowdfunding_products');
    }
}
