Imports System.Drawing
Imports System.Windows.Forms

Public Class WorkerDialog
    Inherits Form

    Public WorkerId As String = ""
    Private db As New DatabaseHelper()

    Private cmbUser As ComboBox
    Private txtContact As TextBox
    Private txtBarangay As TextBox

    Public Sub New()
        Me.Text = "Extension Worker Details"
        Me.Size = New Size(400, 300)
        Me.StartPosition = FormStartPosition.CenterParent
        Me.FormBorderStyle = FormBorderStyle.FixedDialog
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.BackColor = Color.White

        Dim yPos = 20
        Dim xPos = 20
        Dim lblWidth = 100
        Dim ctrlWidth = 240

        ' User selection
        Dim lbl1 As New Label() With {.Text = "Select User:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        cmbUser = New ComboBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .DropDownStyle = ComboBoxStyle.DropDownList}
        Me.Controls.Add(lbl1) : Me.Controls.Add(cmbUser)
        yPos += 40

        ' Contact
        Dim lbl2 As New Label() With {.Text = "Contact No:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        txtContact = New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
        Me.Controls.Add(lbl2) : Me.Controls.Add(txtContact)
        yPos += 40

        ' Barangay
        Dim lbl3 As New Label() With {.Text = "Assigned Brgy:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        txtBarangay = New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
        Me.Controls.Add(lbl3) : Me.Controls.Add(txtBarangay)
        yPos += 50

        ' Buttons
        Dim btnSave As New Button() With {.Text = "Save", .Left = xPos + lblWidth, .Top = yPos, .Width = 100}
        Dim btnCancel As New Button() With {.Text = "Cancel", .Left = xPos + lblWidth + 110, .Top = yPos, .Width = 100}
        
        UIHelper.StylePrimaryButton(btnSave)
        btnCancel.FlatStyle = FlatStyle.Flat
        btnCancel.FlatAppearance.BorderSize = 1
        
        AddHandler btnSave.Click, AddressOf SaveData
        AddHandler btnCancel.Click, Sub() Me.DialogResult = DialogResult.Cancel
        
        Me.Controls.Add(btnSave) : Me.Controls.Add(btnCancel)

        LoadUsers()
    End Sub

    Private Sub LoadUsers()
        Dim dt = db.ExecuteQuery("SELECT id, full_name FROM users WHERE role='extension_worker'")
        cmbUser.DisplayMember = "full_name"
        cmbUser.ValueMember = "id"
        cmbUser.DataSource = dt
    End Sub

    Public Sub LoadData(id As String)
        WorkerId = id
        Dim dt = db.ExecuteQuery("SELECT * FROM extension_workers WHERE id=@id", New Dictionary(Of String, Object) From {{"@id", WorkerId}})
        If dt.Rows.Count > 0 Then
            Dim row = dt.Rows(0)
            cmbUser.SelectedValue = row("user_id")
            txtContact.Text = row("contact_number").ToString()
            txtBarangay.Text = row("assigned_barangay").ToString()
        End If
    End Sub

    Private Sub SaveData()
        If cmbUser.SelectedValue Is Nothing Then
            MessageBox.Show("Please select a user.")
            Return
        End If

        Try
            Dim dict As New Dictionary(Of String, Object) From {
                {"@user_id", cmbUser.SelectedValue},
                {"@contact", txtContact.Text},
                {"@brgy", txtBarangay.Text}
            }

            If String.IsNullOrEmpty(WorkerId) Then
                Dim q = "INSERT INTO extension_workers (user_id, contact_number, assigned_barangay) VALUES (@user_id, @contact, @brgy)"
                db.ExecuteNonQuery(q, dict)
            Else
                Dim q = "UPDATE extension_workers SET user_id=@user_id, contact_number=@contact, assigned_barangay=@brgy WHERE id=@id"
                dict.Add("@id", WorkerId)
                db.ExecuteNonQuery(q, dict)
            End If

            Me.DialogResult = DialogResult.OK
        Catch ex As Exception
            MessageBox.Show("Error saving worker: " & ex.Message)
        End Try
    End Sub
End Class
