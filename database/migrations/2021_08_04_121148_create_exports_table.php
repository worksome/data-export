<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportsTable extends Migration
{
    public function up(): void
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->unsignedBigInteger('account_id')
                ->index();
            $table->string('account_type')
                ->index();
            $table->string('path')
                ->nullable();
            $table->string('status')
                ->index();
            $table->string('type')
                ->index();
            $table->text('delivery');
            $table->text('args')
                ->nullable();
            $table->bigInteger('size')
                ->nullable();
            $table->string('mime_type')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
}
