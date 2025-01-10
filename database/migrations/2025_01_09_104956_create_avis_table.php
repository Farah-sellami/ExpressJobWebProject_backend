<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import de la classe DB
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->text('Commentaire')->nullable(); // Commentaire laissé par le client
            $table->text('Reponse')->nullable(); // Réponse laissée par le professionnel
            $table->unsignedTinyInteger('Rate'); // Note donnée par le client (par exemple, de 1 à 5)
            $table->date('DateAvis')->default(DB::raw('CURRENT_DATE')); // Date de l'avis
            $table->foreignId('client_id') // Clé étrangère vers le client
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('professionnel_id') // Clé étrangère vers le professionnel
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
