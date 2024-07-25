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
        Schema::create('post_user_reacts', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $this->forignKey($table,'post_id');
            $this->forignKey($table,'user_id');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('post_user_reacts');
    }

};
