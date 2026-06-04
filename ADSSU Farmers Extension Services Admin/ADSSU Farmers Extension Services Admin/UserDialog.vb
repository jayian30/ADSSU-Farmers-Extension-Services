Imports System.Drawing
Imports System.Windows.Forms
Imports BCrypt.Net

Public Class UserDialog
    Inherits Form

    Public UserId As String = ""
    Private db As New DatabaseHelper()

    Private txtUsername As TextBox
    Private txtPassword As TextBox
    Private txtFullName As TextBox
    Private cmbRole As ComboBox
    Private txtEmail As TextBox
    Private cmbStatus As ComboBox

    Public Sub New()
        Me.Text = "User Details"
        Me.Size = New Size(400, 480)
        Me.StartPosition = FormStartPosition.CenterParent
        Me.FormBorderStyle = FormBorderStyle.FixedDialog
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.BackColor = Color.White

        Dim yPos = 20
        Dim xPos = 20
        Dim lblWidth = 100
        Dim ctrlWidth = 240

        ' Username
        Dim lbl1 As New Label() With {.Text = "Username:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        txtUsername = New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
        Me.Controls.Add(lbl1) : Me.Controls.Add(txtUsername)
        yPos += 40

        ' Password
        Dim lbl2 As New Label() With {.Text = "Password:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        txtPassword = New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .PasswordChar = "*"}
        Me.Controls.Add(lbl2) : Me.Controls.Add(txtPassword)
        yPos += 40

        ' Full Name
        Dim lbl3 As New Label() With {.Text = "Full Name:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        txtFullName = New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
        Me.Controls.Add(lbl3) : Me.Controls.Add(txtFullName)
        yPos += 40

        ' Role
        Dim lbl4 As New Label() With {.Text = "Role:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        cmbRole = New ComboBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbRole.Items.AddRange({"admin", "extension_worker", "farmer"})
        cmbRole.SelectedIndex = 0
        Me.Controls.Add(lbl4) : Me.Controls.Add(cmbRole)
        yPos += 40

        ' Email
        Dim lbl5 As New Label() With {.Text = "Email:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        txtEmail = New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
        Me.Controls.Add(lbl5) : Me.Controls.Add(txtEmail)
        yPos += 40

        ' Status
        Dim lbl6 As New Label() With {.Text = "Status:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        cmbStatus = New ComboBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbStatus.Items.AddRange({"active", "inactive"})
        cmbStatus.SelectedIndex = 0
        Me.Controls.Add(lbl6) : Me.Controls.Add(cmbStatus)
        yPos += 60

        ' Buttons
        Dim btnSave As New Button() With {.Text = "Save", .Left = xPos + lblWidth, .Top = yPos, .Width = 100}
        Dim btnCancel As New Button() With {.Text = "Cancel", .Left = xPos + lblWidth + 110, .Top = yPos, .Width = 100}
        
        UIHelper.StylePrimaryButton(btnSave)
        btnCancel.FlatStyle = FlatStyle.Flat
        btnCancel.FlatAppearance.BorderSize = 1
        
        AddHandler btnSave.Click, AddressOf SaveData
        AddHandler btnCancel.Click, Sub() Me.DialogResult = DialogResult.Cancel
        
        Me.Controls.Add(btnSave) : Me.Controls.Add(btnCancel)
    End Sub

    Public Sub LoadData(id As String)
        UserId = id
        Dim dt = db.ExecuteQuery("SELECT * FROM users WHERE id=@id", New Dictionary(Of String, Object) From {{"@id", UserId}})
        If dt.Rows.Count > 0 Then
            Dim row = dt.Rows(0)
            txtUsername.Text = row("username").ToString()
            txtFullName.Text = row("full_name").ToString()
            cmbRole.SelectedItem = row("role").ToString()
            txtEmail.Text = row("email").ToString()
            cmbStatus.SelectedItem = row("status").ToString()
        End If
    End Sub

    Private Sub SaveData()
        If String.IsNullOrWhiteSpace(txtUsername.Text) OrElse String.IsNullOrWhiteSpace(txtFullName.Text) Then
            MessageBox.Show("Username and Full Name are required.")
            Return
        End If

        Try
            Dim dict As New Dictionary(Of String, Object) From {
                {"@username", txtUsername.Text},
                {"@full_name", txtFullName.Text},
                {"@role", cmbRole.SelectedItem.ToString()},
                {"@email", txtEmail.Text},
                {"@status", cmbStatus.SelectedItem.ToString()}
            }

            If String.IsNullOrEmpty(UserId) Then
                ' INSERT
                If String.IsNullOrWhiteSpace(txtPassword.Text) Then
                    MessageBox.Show("Password is required for new users.")
                    Return
                End If
                dict.Add("@password", BCrypt.Net.BCrypt.HashPassword(txtPassword.Text))
                
                Dim q = "INSERT INTO users (username, password, full_name, role, email, status) VALUES (@username, @password, @full_name, @role, @email, @status)"
                db.ExecuteNonQuery(q, dict)
            Else
                ' UPDATE
                Dim q = "UPDATE users SET username=@username, full_name=@full_name, role=@role, email=@email, status=@status"
                If Not String.IsNullOrWhiteSpace(txtPassword.Text) Then
                    dict.Add("@password", BCrypt.Net.BCrypt.HashPassword(txtPassword.Text))
                    q &= ", password=@password"
                End If
                q &= " WHERE id=@id"
                dict.Add("@id", UserId)
                db.ExecuteNonQuery(q, dict)
            End If

            Me.DialogResult = DialogResult.OK
        Catch ex As Exception
            MessageBox.Show("Error saving user: " & ex.Message)
        End Try
    End Sub
End Class
