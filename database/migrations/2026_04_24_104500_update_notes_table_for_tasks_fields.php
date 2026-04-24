<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table): void {
            $table->renameColumn('content', 'description');
        });

        Schema::table('notes', function (Blueprint $table): void {
            $table->string('statu')->default('pending');
        });

        DB::table('notes')->whereNull('statu')->update(['statu' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table): void {
            $table->dropColumn('statu');
        });

        Schema::table('notes', function (Blueprint $table): void {
            $table->renameColumn('description', 'content');
        });
    }
};
