Imports System.Drawing
Imports System.Windows.Forms

Public Class ProgramDialog
    Inherits Form

    Public ProgramId As String = ""
    Private db As New DatabaseHelper()

    Private txtName As TextBox
    Private txtDesc As TextBox
    Private dtpStart As DateTimePicker
    Private dtpEnd As DateTimePicker
    Private cmbStatus As ComboBox

    Public Sub New()
        Me.Text = "Agricultural Program Details"
        Me.Size = New Size(420, 400)
        Me.StartPosition = FormStartPosition.CenterParent
        Me.FormBorderStyle = FormBorderStyle.FixedDialog
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.BackColor = Color.White

        Dim yPos = 20
        Dim xPos = 20
        Dim lblWidth = 100
        Dim ctrlWidth = 260

        Dim AddField = Function(label As String, isMultiline As Boolean) As TextBox
                           Dim lbl As New Label() With {.Text = label, .Left = xPos, .Top = yPos, .Width = lblWidth}
                           Dim txt As New TextBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth}
                           If isMultiline Then
                               txt.Multiline = True
                               txt.Height = 60
                           End If
                           Me.Controls.Add(lbl) : Me.Controls.Add(txt)
                           yPos += If(isMultiline, 70, 40)
                           Return txt
                       End Function

        txtName = AddField("Program Name:", False)
        txtDesc = AddField("Description:", True)

        Dim lblStart As New Label() With {.Text = "Start Date:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        dtpStart = New DateTimePicker() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .Format = DateTimePickerFormat.Short}
        Me.Controls.Add(lblStart) : Me.Controls.Add(dtpStart)
        yPos += 40

        Dim lblEnd As New Label() With {.Text = "End Date:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        dtpEnd = New DateTimePicker() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .Format = DateTimePickerFormat.Short}
        Me.Controls.Add(lblEnd) : Me.Controls.Add(dtpEnd)
        yPos += 40

        Dim lblStatus As New Label() With {.Text = "Status:", .Left = xPos, .Top = yPos, .Width = lblWidth}
        cmbStatus = New ComboBox() With {.Left = xPos + lblWidth, .Top = yPos, .Width = ctrlWidth, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbStatus.Items.AddRange({"planned", "active", "completed"})
        cmbStatus.SelectedIndex = 0
        Me.Controls.Add(lblStatus) : Me.Controls.Add(cmbStatus)
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
        ProgramId = id
        Dim dt = db.ExecuteQuery("SELECT * FROM agricultural_programs WHERE id=@id", New Dictionary(Of String, Object) From {{"@id", ProgramId}})
        If dt.Rows.Count > 0 Then
            Dim row = dt.Rows(0)
            txtName.Text = row("program_name").ToString()
            txtDesc.Text = row("description").ToString()
            If Not DBNull.Value.Equals(row("start_date")) Then dtpStart.Value = Convert.ToDateTime(row("start_date"))
            If Not DBNull.Value.Equals(row("end_date")) Then dtpEnd.Value = Convert.ToDateTime(row("end_date"))
            cmbStatus.SelectedItem = row("status").ToString()
        End If
    End Sub

    Private Sub SaveData()
        Try
            Dim dict As New Dictionary(Of String, Object) From {
                {"@name", txtName.Text},
                {"@desc", txtDesc.Text},
                {"@start", dtpStart.Value.ToString("yyyy-MM-dd")},
                {"@end", dtpEnd.Value.ToString("yyyy-MM-dd")},
                {"@status", cmbStatus.SelectedItem.ToString()}
            }

            If String.IsNullOrEmpty(ProgramId) Then
                Dim q = "INSERT INTO agricultural_programs (program_name, description, start_date, end_date, status) VALUES (@name, @desc, @start, @end, @status)"
                db.ExecuteNonQuery(q, dict)
            Else
                Dim q = "UPDATE agricultural_programs SET program_name=@name, description=@desc, start_date=@start, end_date=@end, status=@status WHERE id=@id"
                dict.Add("@id", ProgramId)
                db.ExecuteNonQuery(q, dict)
            End If

            Me.DialogResult = DialogResult.OK
        Catch ex As Exception
            MessageBox.Show("Error saving program: " & ex.Message)
        End Try
    End Sub
End Class
