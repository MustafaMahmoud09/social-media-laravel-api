<?php

use App\Traits\Migration\ForignKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use ForignKey;

    public function up(): void
    {
        Schema::create('gender_users', function (Blueprint $table) {
            $table->id();
            $this->forignKey($table,'user_id');
            $this->forignKey($table,'gender_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gender_users');
    }
};
