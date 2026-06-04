Imports MySqlConnector
Imports System.Data

Public Class DatabaseHelper
    Private connectionString As String = "Server=localhost;Port=3307;Database=adssu_farmers_db;Uid=root;Pwd=;Charset=utf8mb4;"
    Private connection As MySqlConnection

    Public Sub New()
        connection = New MySqlConnection(connectionString)
    End Sub

    ' Open the connection
    Public Sub OpenConnection()
        If connection.State = ConnectionState.Closed Then
            Try
                connection.Open()
            Catch ex As Exception
                MessageBox.Show("Database connection error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If
    End Sub

    ' Close the connection
    Public Sub CloseConnection()
        If connection.State = ConnectionState.Open Then
            connection.Close()
        End If
    End Sub

    ' Get the connection object
    Public Function GetConnection() As MySqlConnection
        Return connection
    End Function

    ' Execute a query (Insert, Update, Delete) and return affected rows
    Public Function ExecuteNonQuery(query As String, Optional parameters As Dictionary(Of String, Object) = Nothing) As Integer
        Dim affectedRows As Integer = 0
        Try
            OpenConnection()
            Using cmd As New MySqlCommand(query, connection)
                If parameters IsNot Nothing Then
                    For Each kvp In parameters
                        cmd.Parameters.AddWithValue(kvp.Key, kvp.Value)
                    Next
                End If
                affectedRows = cmd.ExecuteNonQuery()
            End Using
        Catch ex As Exception
            MessageBox.Show("Execution Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Finally
            CloseConnection()
        End Try
        Return affectedRows
    End Function

    ' Execute a query and return a DataTable (Select)
    Public Function ExecuteQuery(query As String, Optional parameters As Dictionary(Of String, Object) = Nothing) As DataTable
        Dim dataTable As New DataTable()
        Try
            OpenConnection()
            Using cmd As New MySqlCommand(query, connection)
                If parameters IsNot Nothing Then
                    For Each kvp In parameters
                        cmd.Parameters.AddWithValue(kvp.Key, kvp.Value)
                    Next
                End If
                Using adapter As New MySqlDataAdapter(cmd)
                    adapter.Fill(dataTable)
                End Using
            End Using
        Catch ex As Exception
            MessageBox.Show("Query Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Finally
            CloseConnection()
        End Try
        Return dataTable
    End Function

    ' Execute a scalar query (e.g., COUNT(*))
    Public Function ExecuteScalar(query As String, Optional parameters As Dictionary(Of String, Object) = Nothing) As Object
        Dim result As Object = Nothing
        Try
            OpenConnection()
            Using cmd As New MySqlCommand(query, connection)
                If parameters IsNot Nothing Then
                    For Each kvp In parameters
                        cmd.Parameters.AddWithValue(kvp.Key, kvp.Value)
                    Next
                End If
                result = cmd.ExecuteScalar()
            End Using
        Catch ex As Exception
            MessageBox.Show("Query Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Finally
            CloseConnection()
        End Try
        Return result
    End Function
End Class
