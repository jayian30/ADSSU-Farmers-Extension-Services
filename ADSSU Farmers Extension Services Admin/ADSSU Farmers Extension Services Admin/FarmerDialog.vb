Imports System.Drawing
Imports System.Windows.Forms

Public Class FarmerDialog
    Inherits Form

    Public FarmerId As String = ""
    Private db As New DatabaseHelper()

    Private txtRsbsa As TextBox
    Private txtFullName As TextBox
    Private txtAddress As TextBox
    Private txtBarangay As TextBox
    Private txtContact As TextBox
    Private txtFarmType As TextBox
    Private txtCropType As TextBox
    Private txtFarmSize As TextBox
    Private cmbStatus As ComboBox

    Public Sub New()
        Me.Text = "Farmer Details"
        Me.Size = New Size(420, 560)
        Me.StartPosition = FormStartPosition.CenterParent
        Me.FormBorderStyle = FormBorderStyle.FixedDialog
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.BackColor = Color.White

        Dim yPos = 20
        Dim xPos = 20
        Dim lblWidth = 100
        Dim ctrlWidth = 260

        Dim AddField = Function(label As String) As TextBox
                           Dim lbl As New Label() With {.Text = label, .Left = xPos, .Top = yPos, .Width = lblWidth}
                           Dim txt As New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
                           Me.Controls.Add(lbl) : Me.Controls.Add(txt)
                           yPos += 35
                           Return txt
                       End Function

        txtRsbsa = AddField("RSBSA No:")
        txtFullName = AddField("Full Name:")
        txtAddress = AddField("Address:")
        txtBarangay = AddField("Barangay:")
        txtContact = AddField("Contact No:")
        txtFarmType = AddField("Farm Type:")
        txtCropType = AddField("Crop Type:")
        txtFarmSize = AddField("Farm Size (ha):")

        Dim lbl6 As New Label() With {.Text = "Status:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        cmbStatus = New ComboBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbStatus.Items.AddRange({"active", "inactive"})
        cmbStatus.SelectedIndex = 0
        Me.Controls.Add(lbl6) : Me.Controls.Add(cmbStatus)
        yPos += 50

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
        FarmerId = id
        Dim dt = db.ExecuteQuery("SELECT * FROM farmers WHERE id=@id", New Dictionary(Of String, Object) From {{"@id", FarmerId}})
        If dt.Rows.Count > 0 Then
            Dim row = dt.Rows(0)
            txtRsbsa.Text = row("rsbsa_number").ToString()
            txtFullName.Text = row("full_name").ToString()
            txtAddress.Text = row("address").ToString()
            txtBarangay.Text = row("barangay").ToString()
            txtContact.Text = row("contact_number").ToString()
            txtFarmType.Text = row("farm_type").ToString()
            txtCropType.Text = row("crop_type").ToString()
            txtFarmSize.Text = row("farm_size").ToString()
            cmbStatus.SelectedItem = row("status").ToString()
        End If
    End Sub

    Private Sub SaveData()
        Try
            Dim dict As New Dictionary(Of String, Object) From {
                {"@rsbsa", If(String.IsNullOrWhiteSpace(txtRsbsa.Text), DBNull.Value, txtRsbsa.Text)},
                {"@name", txtFullName.Text},
                {"@addr", txtAddress.Text},
                {"@brgy", txtBarangay.Text},
                {"@contact", txtContact.Text},
                {"@ftype", txtFarmType.Text},
                {"@ctype", txtCropType.Text},
                {"@fsize", If(String.IsNullOrWhiteSpace(txtFarmSize.Text), DBNull.Value, txtFarmSize.Text)},
                {"@status", cmbStatus.SelectedItem.ToString()}
            }

            If String.IsNullOrEmpty(FarmerId) Then
                Dim q = "INSERT INTO farmers (rsbsa_number, full_name, address, barangay, contact_number, farm_type, crop_type, farm_size, status) VALUES (@rsbsa, @name, @addr, @brgy, @contact, @ftype, @ctype, @fsize, @status)"
                db.ExecuteNonQuery(q, dict)
            Else
                Dim q = "UPDATE farmers SET rsbsa_number=@rsbsa, full_name=@name, address=@addr, barangay=@brgy, contact_number=@contact, farm_type=@ftype, crop_type=@ctype, farm_size=@fsize, status=@status WHERE id=@id"
                dict.Add("@id", FarmerId)
                db.ExecuteNonQuery(q, dict)
            End If

            Me.DialogResult = DialogResult.OK
        Catch ex As Exception
            MessageBox.Show("Error saving farmer: " & ex.Message)
        End Try
    End Sub
End Class
