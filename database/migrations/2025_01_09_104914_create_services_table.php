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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('Titre'); // Titre du service
            $table->text('Description')->nullable(); // Description du service (optionnel)
            $table->date('DateCreation')->nullable(); // Date de création du service (optionnel)
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade'); // Clé étrangère vers la table `categories`
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
