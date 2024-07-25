<?php

namespace App\Traits\Migration;

use Illuminate\Database\Schema\Blueprint;

trait ForignKey
{

    public function forignKey(Blueprint $table, $name, $table_name = null, $nulable = null)
    {
        $table = $table->foreignId($name);

        if ($nulable) {
           $table = $table->nullable();
        }
        return $table->constrained($table_name)
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
    }
}
