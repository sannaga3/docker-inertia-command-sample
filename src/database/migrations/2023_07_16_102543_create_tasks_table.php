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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('タイトル');
            $table->string('content')->comment('コンテンツ');
            $table->date('date')->comment('作成日');
            $table->boolean('finished')->default(false)->comment('完了');
            $table->boolean('published')->default(true)->comment('公開');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};