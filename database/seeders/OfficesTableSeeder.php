<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('offices')->where('office_name', 'Office of the Schools Division Superintendent')->update(['prefix' => 'SDS']);
        DB::table('offices')->where('office_name', 'Office of the Assistant Schools Division Superintendent ')->update(['prefix' => 'ASDS']);
        DB::table('offices')->where('office_name', 'Administration Section')->update(['prefix' => 'AS']);
        DB::table('offices')->where('office_name', 'Curriculum Implementation Division')->update(['prefix' => 'CID']);
        DB::table('offices')->where('office_name', 'Accounting and Budget Section')->update(['prefix' => 'ABS']);
        DB::table('offices')->where('office_name', 'Information and Communication Technology')->update(['prefix' => 'ICT']);
        DB::table('offices')->where('office_name', 'Legal Section')->update(['prefix' => 'LS']);
        DB::table('offices')->where('office_name', 'School Governance and Operations Division')->update(['prefix' => 'SGOD']);
        DB::table('offices')->where('office_name', 'Schools')->update(['prefix' => 'SCH']);
    }
}