<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Rules\CheckDatabaseExistRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function createDB(Request $request)
    {
        $request->validate([
            'name' => ['required', new CheckDatabaseExistRole()],
        ]);
        $dbName = $request->name;
        DB::statement("CREATE DATABASE $dbName");
        $success = "success";
        return response()->json($success);
    }
    public function getDB()
    {

        $database = DB::select('SHOW DATABASES');
        return response()->json($database);


        //    $tableNames = [];
        //     $tableNames2 = [];
        //         DB::disconnect('mysql');
        //         Config::set('database.connections.mysql.database', 'atlas');
        //         $tables = DB::select('SHOW TABLES');
        //         foreach ($tables as $table) {
        //             $tableNames[] = $table->Tables_in_atlas;
        //         }
        //         var_dump($tableNames);
        //         DB::disconnect('mysql');
        //         Config::set('database.connections.mysql.database', 'etl_test');
        //         $tables2 = DB::select('SHOW TABLES');
        //         foreach ($tables2 as $table2) {
        //             $tableNames2[] = $table2->Tables_in_etl_test;
        //         }
    }
    public function createTable(Request $request)
    {
        DB::disconnect('mysql');
        Config::set('database.connections.mysql.database', $request->db);
        $query = $request->tableQuery;
        DB::statement($query);

        $data = $request->data;
        $column = $request->headerTable;
        $count = 0;
        foreach($data as $record){
            $columnsQuery = '';
            for($i = 0; $i < count($column); $i++){
                if(count($column) -1 == $i ){
                    $columnsQuery .= "'".$record[$column[$i]]."'" ;
                }else {
                    $columnsQuery .= "'".$record[$column[$i]]."', " ;
                }
            }
            $insert = DB::insert("INSERT INTO  `". $request->table[0] ."`  VALUES (". $columnsQuery.")");
            if($insert) {
                $count = $count + 1;
            }
        }

        DB::disconnect('mysql');
        Config::set('database.connections.mysql.database', 'projet_etl');
            Log::create([
                'nom_fichier' => $request->table[0].'.'.$request->table[1],
                'date_tranfert' => Carbon::now(),
                'nbr_enregistrement' => $count,
                'user_name' => Auth::user()->name,
                'saved_in_db' => $request->db,
                'saved_in_table' => $request->table[0],
            ]);
        return response()->json($record);
    }
}
