<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('show_in_menu')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categories');
    }
};
