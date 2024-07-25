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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->text("state")->nullable();
            $table->integer("theme");
            $table->integer("validity");
            $this->forignKey($table,'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
