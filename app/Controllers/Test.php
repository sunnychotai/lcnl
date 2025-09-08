<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

class Test extends Controller
{
    public function dbcheck()
    {
        try {
            $db = Database::connect();
            $query = $db->query("SELECT DATABASE() AS dbname");
            $row = $query->getRow();
            return "✅ Connected to database: " . $row->dbname;
        } catch (\Throwable $e) {
            return "❌ DB connection failed: " . $e->getMessage();
        }
    }
}
