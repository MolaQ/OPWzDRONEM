<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');

            // Status rezerwacji
            $table->enum('status', [
                'pending',      // Oczekuje na potwierdzenie
                'confirmed',    // Potwierdzona
                'used',         // Użyta
                'cancelled',    // Anulowana
                'no_show'       // Nie pojawił się
            ])->default('pending');

            // Daty rezerwacji
            $table->timestamp('reserved_from')->useCurrent();
            $table->timestamp('reserved_until')->useCurrent();

            // Kto rezerwuje i komentarze
            $table->text('reason')->nullable(); // Powód rezerwacji
            $table->text('notes')->nullable();

            // Czy została użyta i kiedy faktycznie
            $table->timestamp('actual_checkout_at')->nullable();
            $table->timestamp('actual_checkin_at')->nullable();

            // Kto potwierdził/anulował
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('equipment_id');
            $table->index('user_id');
            $table->index('group_id');
            $table->index(['reserved_from', 'reserved_until']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_reservations');
    }
};
