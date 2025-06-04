<?php
// database/migrations/2025_05_30_000000_add_queue_id_to_tokens.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQueueIdToTokens extends Migration
{
    public function up()
    {
        Schema::table('tokens', function (Blueprint $table) {
            // add the new FK
            $table->unsignedBigInteger('queue_id')->nullable()->after('id');
            $table->foreign('queue_id')
                  ->references('id')
                  ->on('queues')
                  ->onDelete('cascade');
            // drop the old department_id if you’ve already removed it from your schema:
            if (Schema::hasColumn('tokens','department_id')) {
              $table->dropForeign(['department_id']);
              $table->dropColumn('department_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropForeign(['queue_id']);
            $table->dropColumn('queue_id');
            // (optionally) re-add department_id here
        });
    }
}
