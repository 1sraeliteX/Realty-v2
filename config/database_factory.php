<?php

namespace Config;

class DatabaseFactory {
    public static function create() {
        $useSupabase = ConfigSimple::getInstance()->get('database.use_supabase', false);
        
        if ($useSupabase) {
            return SupabaseDatabase::getInstance();
        } else {
            return Database::getInstance();
        }
    }
}
