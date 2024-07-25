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
        Schema::create('user_documentations', function (Blueprint $table) {
            $table->id();
            $this->forignKey($table,'admin_id');
            $this->forignKey($table,'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documentations');
    }
};
