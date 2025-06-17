// database/seeders/QueueSeeder.php

use Illuminate\Database\Seeder;
use App\Models\Queue;

class QueueSeeder extends Seeder
{
    public function run()
    {
        $general = Queue::firstOrCreate(['name'=>'General']);

        $winA = Queue::firstOrCreate([
            'name'      => 'Window A',
            'parent_id' => $general->id,
        ]);
        $winB = Queue::firstOrCreate([
            'name'      => 'Window B',
            'parent_id' => $general->id,
        ]);

        foreach (['Gynecology','Internal Medicine','Well’Come Teens','OPD Pay'] as $d) {
            Queue::firstOrCreate(['name'=>$d,'parent_id'=>$winA->id]);
        }
        foreach (['Pediatrics','OB'] as $d) {
            Queue::firstOrCreate(['name'=>$d,'parent_id'=>$winB->id]);
        }
    }
}
