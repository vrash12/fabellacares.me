<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/xxxx_xx_xx_make_submission_id_nullable_on_tokens.php
return new class extends Migration {
    public function up() {
        Schema::table('tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('submission_id')->nullable()->change();
        });
    }
    public function down() {
        Schema::table('tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('submission_id')->nullable(false)->change();
        });
    }
};
