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
        // Vérifier s'il n'y a pas déjà un admin dans la base de données
        if (DB::table('users')->where('role', 'admin')->doesntExist()) {
            DB::table('users')->insert([
                'name' => 'Admin Principal',
                'email' => 'farah.sellami@gmail.com',  // Remplacez par l'email souhaité
                'password' => Hash::make('azerty'),
                'role' => 'admin',
                'isActive' => true,
                'avatar' => 'https://res.cloudinary.com/defx74d1x/image/upload/v1737276011/icons8-user-100_lxppdo.png',
                'telephone' => '0000000000',
                'adresse' => 'Sfax City',
                'competence' => null,
                'available_hours' => null,
                'note_moyenne' => null,
                'location' => 'Admin Location',
                'service_id' => null,
                'last_connection' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Vous pouvez définir ici ce qui doit se passer si la migration est annulée
        DB::table('users')->where('role', 'admin')->delete();
    }
};
