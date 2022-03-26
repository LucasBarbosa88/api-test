<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BankController extends Controller
{
    public function reset()
    {
        $tables = DB::select('SHOW TABLES');
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            foreach($table as $table_name){
                if ($table_name == 'migrations') {
                    continue;
                }
                DB::table($table_name)->truncate();
            }
        }
        Schema::enableForeignKeyConstraints();

        return response('OK', 200);
    }
}
