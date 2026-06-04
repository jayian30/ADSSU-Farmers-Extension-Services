Imports System.Data
Imports BCrypt.Net

Public Class Form1
    Private db As New DatabaseHelper()

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        lblError.Text = ""
    End Sub

    Private Sub btnLogin_Click(sender As Object, e As EventArgs) Handles btnLogin.Click
        lblError.Text = ""
        Dim username As String = txtUsername.Text.Trim()
        Dim password As String = txtPassword.Text

        If String.IsNullOrEmpty(username) OrElse String.IsNullOrEmpty(password) Then
            lblError.Text = "Please enter both username and password."
            Return
        End If

        Try
            ' Get the user from the database
            Dim query As String = "SELECT id, password, role FROM users WHERE username = @Username AND role = 'admin'"
            Dim parameters As New Dictionary(Of String, Object) From {
                {"@Username", username}
            }

            Dim dt As DataTable = db.ExecuteQuery(query, parameters)

            If dt.Rows.Count = 1 Then
                Dim hash As String = dt.Rows(0)("password").ToString()
                
                ' Verify the password
                If BCrypt.Net.BCrypt.Verify(password, hash) Then
                    ' Success! Open Dashboard
                    Dim dashboard As New Form2()
                    dashboard.Show()
                    Me.Hide()
                Else
                    lblError.Text = "Invalid username or password."
                End If
            Else
                lblError.Text = "Invalid username or password."
            End If

        Catch ex As Exception
            MessageBox.Show("Login Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    Private Sub txtPassword_KeyDown(sender As Object, e As KeyEventArgs) Handles txtPassword.KeyDown
        If e.KeyCode = Keys.Enter Then
            btnLogin.PerformClick()
            e.SuppressKeyPress = True
        End If
    End Sub
End Class
