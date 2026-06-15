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
        Schema::table('internships', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $table->addColumn('tsvector', 'search_vector')->nullable()->after('description');
                $table->index('search_vector', 'internships_search_vector_index', 'gin');
            } else {
                $table->text('search_vector')->nullable()->after('description');
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            // Add trigger to update search_vector automatically
            DB::statement("
                CREATE TRIGGER internships_search_vector_update BEFORE INSERT OR UPDATE
                ON internships FOR EACH ROW EXECUTE FUNCTION
                tsvector_update_trigger(search_vector, 'pg_catalog.indonesian', title, description);
            ");

            // Populate existing records
            DB::statement('UPDATE internships SET updated_at = NOW()');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS internships_search_vector_update ON internships');

            Schema::table('internships', function (Blueprint $table) {
                $table->dropIndex('internships_search_vector_index');
                $table->dropColumn('search_vector');
            });
        } else {
            Schema::table('internships', function (Blueprint $table) {
                $table->dropColumn('search_vector');
            });
        }
    }
};
