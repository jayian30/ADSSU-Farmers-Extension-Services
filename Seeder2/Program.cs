using System;
using System.IO;
using MySqlConnector;

class Program
{
    static void Main()
    {
        string connStr = "Server=localhost;Port=3307;Database=adssu_farmers_db;Uid=root;Pwd=;Charset=utf8mb4;";
        string sql = File.ReadAllText(@"..\seed2.sql");
        
        using (var conn = new MySqlConnection(connStr))
        {
            conn.Open();
            using (var cmd = new MySqlCommand(sql, conn))
            {
                cmd.ExecuteNonQuery();
                Console.WriteLine("Seed2 data inserted successfully.");
            }
        }
    }
}
