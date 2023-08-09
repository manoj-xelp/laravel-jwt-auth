<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CountryStateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * json file source: https://github.com/dr5hn/countries-states-cities-database
     */
    public function run(): void
    {
        $this->command->info("Since the data set is too big, this will take upto 20-30 Minutes to complete!");
        $startTime = microtime(true);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cities')->truncate();
        DB::table('states')->truncate();
        DB::table('countries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json = \File::json(public_path('assets/CountriesStatesCities.json'));

        $json= json_decode(json_encode($json));      
        
        collect($json)->chunk(50)->each(function($countries) {
            $country_chunk=[];
            foreach($countries as $c){
                $country_data= [
                    'id' => $c->id,
                    'name' => $c->name,
                    'iso2' => $c->iso2,
                    'phone_code' => $c->phone_code,
                    'emoji' => $c->emoji
                ];
                array_push($country_chunk,$country_data);
            } 
            DB::table('countries')->insert($country_chunk);
        });

        foreach($json as $state_data){
            collect($state_data->states)->chunk(100)->each(function($states) use($state_data){
                $state_chunk=[];
                foreach($states as $s){
                    $s_data= [
                        'id' => $s->id,
                        'country_id' => $state_data->id,
                        'name' => $s->name,
                        'state_code' => $s->state_code
                    ];
                    array_push($state_chunk,$s_data);
                } 
                DB::table('states')->insert($state_chunk);
            });

            foreach($state_data->states as $city_data){
                collect($city_data->cities)->chunk(500)->each(function($cities) use($city_data,$state_data){
                    $city_chunk=[];
                    foreach($cities as $city){
                        $c_data= [
                            'id' => $city->id,
                            'country_id' => $state_data->id,
                            'state_id' => $city_data->id,
                            'name' => $city->name
                        ];
                        array_push($city_chunk,$c_data);
                    } 
                    DB::table('cities')->insert($city_chunk);
                });
            }
        }

        $endTime = microtime(true);   
        $executionTime = $endTime - $startTime;
        $this->command->info("Seeder took " . $executionTime . " seconds to Finish.");
    }
}
