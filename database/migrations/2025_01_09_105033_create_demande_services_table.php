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
        Schema::create('demande_services', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id'); // Clé étrangère pour le client
            $table->unsignedBigInteger('professionnel_id'); // Clé étrangère pour le professionnel
            $table->date('DateDemande')->default(DB::raw('CURRENT_DATE')); // Date avec la date système par défaut
            $table->enum('Statut', ['en_attente', 'terminé', 'annulé'])->default('en_attente');
            $table->date('DateExecution')->nullable(); // Date d'exécution de la demande
            $table->timestamps();

            // Définir la clé primaire composite
            $table->primary(['client_id', 'professionnel_id', 'DateDemande']);

            // Clés étrangères
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professionnel_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_services');
    }
};
