using System;
using MySqlConnector;

class Program
{
    static void Main()
    {
        string connStr = "Server=localhost;Port=3307;Database=adssu_farmers_db;Uid=root;Pwd=;Charset=utf8mb4;";
        string sql = @"
            ALTER TABLE farmers ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
            ALTER TABLE extension_workers ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
        ";
        
        using (var conn = new MySqlConnection(connStr))
        {
            conn.Open();
            using (var cmd = new MySqlCommand(sql, conn))
            {
                try {
                    cmd.ExecuteNonQuery();
                    Console.WriteLine("Altered tables successfully.");
                } catch (Exception ex) {
                    Console.WriteLine(ex.Message);
                }
            }
        }
    }
}
