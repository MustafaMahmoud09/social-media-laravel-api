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
        Schema::create('message_user_watches', function (Blueprint $table) {
            $table->id();
            $this->forignKey($table,"user_id");
            $this->forignKey($table,"message_id");
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('message_user_watches');
    }
};
