<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (DB::table('categories')->where('slug', 'domain')->exists()) {
            return;
        }
        // Seed categories table
        DB::table('categories')->insert([
            [
                'name' => 'Domain',
                'slug' => 'domain',
                'description' => 'Domain name registration and services.'
            ],
        ]);

        // Retrieve the category_id of the newly inserted category
        $domainCategoryId = DB::table('categories')->where('slug', 'domain')->value('id');

        // Seed products table
        DB::table('products')->insert([
            [
                'category_id' => $domainCategoryId,
                'name' => 'Domain Name',
                'slug' => 'domain-name',
                'description' => 'A domain name to establish your online presence.'
            ],
        ]);

        // Retrieve the product_id of the newly inserted product
        $domainProductId = DB::table('products')->where('slug', 'domain-name')->where('category_id', $domainCategoryId)->value('id');

        // Seed plans table
        DB::table('plans')->insert([
            [
                'name' => 'Basic Plan',
                'priceable_type' => 'App\Models\Product',
                'priceable_id' => $domainProductId,
                'type' => 'recurring',
                'billing_period' => 1,
                'billing_unit' => 'year',
            ],
        ]);

        // Retrieve the plan_id of the newly inserted plan
        $basicPlanId = DB::table('plans')->where('name', 'Basic Plan')
            ->where('priceable_id', $domainProductId)
            ->where('priceable_type', 'App\Models\Product')->value('id');

        // Seed prices table
        DB::table('prices')->insert([
            [
                'price' => 250.00,
                'setup_fee' => 2.00,
                'currency_code' => 'USD',
                'plan_id' => $basicPlanId
            ],
        ]);

        // Seed config_options table
        DB::table('config_options')->insert([
            [
                'name' => 'domain',
                'env_variable' => 'domain',
                'type' => 'text',
                'hidden' => false
            ],
        ]);

        // Retrieve the config_option_id of the newly inserted config option
        $autoRenewOptionId = DB::table('config_options')->where('name', 'domain')->where('env_variable', 'domain')->where('type', 'text')->value('id');

        // Seed config_option_products table
        DB::table('config_option_products')->insert([
            [
                'config_option_id' => $autoRenewOptionId,
                'product_id' => $domainProductId
            ],
        ]);



        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Service::class, 'service_id')->constrained()->nullOnDelete();
            $table->string('register_name')->nullable(false);
            $table->string('domain')->unique()->nullable(false);
            $table->string('ns1', 255)->nullable();
            $table->string('ns2', 255)->nullable();
            $table->string('ns3', 255)->nullable();
            $table->string('ns4', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
