Imports System.Drawing
Imports System.Windows.Forms

Public Class FarmersControl
    Inherits UserControl

    Private db As New DatabaseHelper()
    Private dgv As DataGridView
    Private btnAdd As Button
    Private btnEdit As Button
    Private btnRefresh As Button
    Private btnDelete As Button
    Private btnPrint As Button
    Private btnExport As Button
    Private lblFilter As Label
    Private cmbBarangay As ComboBox

    Public Sub New()
        Me.Padding = New Padding(20)

        ' Header Panel
        Dim pnlHeader As New Panel()
        pnlHeader.Height = 50
        pnlHeader.Dock = DockStyle.Top
        Me.Controls.Add(pnlHeader)

        btnAdd = New Button() With {.Text = "Add New", .Width = 100, .Height = 30, .Top = 10, .Left = 0}
        btnEdit = New Button() With {.Text = "Edit", .Width = 100, .Height = 30, .Top = 10, .Left = 110}
        btnRefresh = New Button() With {.Text = "Refresh", .Width = 100, .Height = 30, .Top = 10, .Left = 220}
        btnDelete = New Button() With {.Text = "Delete Farmer", .Width = 120, .Height = 30, .Top = 10, .Left = 330}
        btnPrint = New Button() With {.Text = "Print", .Width = 100, .Height = 30, .Top = 10, .Left = 460}
        btnExport = New Button() With {.Text = "Export", .Width = 100, .Height = 30, .Top = 10, .Left = 570}
        
        UIHelper.StylePrimaryButton(btnAdd)
        btnAdd.BackColor = Color.FromArgb(76, 175, 80) ' Green
        UIHelper.StylePrimaryButton(btnEdit)
        btnEdit.BackColor = Color.FromArgb(255, 152, 0) ' Orange
        UIHelper.StylePrimaryButton(btnRefresh)
        UIHelper.StyleDangerButton(btnDelete)
        UIHelper.StylePrimaryButton(btnPrint)
        btnPrint.BackColor = Color.FromArgb(33, 150, 243) ' Blue
        UIHelper.StylePrimaryButton(btnExport)
        btnExport.BackColor = Color.FromArgb(156, 39, 176) ' Purple

        AddHandler btnAdd.Click, AddressOf AddFarmer
        AddHandler btnEdit.Click, AddressOf EditFarmer
        AddHandler btnRefresh.Click, AddressOf LoadData
        AddHandler btnDelete.Click, AddressOf DeleteFarmer
        AddHandler btnPrint.Click, AddressOf PrintData
        AddHandler btnExport.Click, AddressOf ExportData

        pnlHeader.Controls.Add(btnAdd)
        pnlHeader.Controls.Add(btnEdit)
        pnlHeader.Controls.Add(btnRefresh)
        pnlHeader.Controls.Add(btnDelete)
        pnlHeader.Controls.Add(btnPrint)
        pnlHeader.Controls.Add(btnExport)
        
        lblFilter = New Label() With {.Text = "Filter:", .Top = 15, .Left = 690, .Width = 40}
        
        cmbBarangay = New ComboBox() With {.Top = 12, .Left = 730, .Width = 170, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbBarangay.Items.Add("All Barangays")
        Try
            Dim dtBrgy = db.ExecuteQuery("SELECT DISTINCT barangay FROM farmers WHERE barangay IS NOT NULL AND barangay != '' ORDER BY barangay")
            For Each row As DataRow In dtBrgy.Rows
                cmbBarangay.Items.Add(row("barangay").ToString())
            Next
        Catch ex As Exception
        End Try
        cmbBarangay.SelectedIndex = 0
        
        AddHandler cmbBarangay.SelectedIndexChanged, AddressOf LoadData
        
        pnlHeader.Controls.Add(lblFilter)
        pnlHeader.Controls.Add(cmbBarangay)

        ' Grid
        dgv = New DataGridView()
        dgv.Dock = DockStyle.Fill
        dgv.AllowUserToAddRows = False
        dgv.AllowUserToDeleteRows = False
        dgv.ReadOnly = True
        
        Me.Controls.Add(dgv)
        
        UIHelper.StyleGrid(dgv)
        dgv.BringToFront()
    End Sub

    Public Sub LoadData()
        Try
            Dim query As String = "SELECT id, rsbsa_number, full_name, address, barangay, contact_number, farm_type, crop_type, farm_size, status, created_at FROM farmers WHERE 1=1"
            Dim parameters As New Dictionary(Of String, Object)
            
            If cmbBarangay IsNot Nothing AndAlso cmbBarangay.SelectedIndex > 0 Then
                query &= " AND barangay = @brgy"
                parameters.Add("@brgy", cmbBarangay.SelectedItem.ToString())
            End If
            
            Dim dt = db.ExecuteQuery(query, parameters)
            dgv.DataSource = dt
        Catch ex As Exception
            MessageBox.Show("Error loading farmers: " & ex.Message)
        End Try
    End Sub

    Private Sub AddFarmer()
        Dim dlg As New FarmerDialog()
        If dlg.ShowDialog() = DialogResult.OK Then
            LoadData()
        End If
    End Sub

    Private Sub EditFarmer()
        If dgv.SelectedRows.Count > 0 Then
            Dim id = dgv.SelectedRows(0).Cells("id").Value.ToString()
            Dim dlg As New FarmerDialog()
            dlg.LoadData(id)
            If dlg.ShowDialog() = DialogResult.OK Then
                LoadData()
            End If
        Else
            MessageBox.Show("Please select a farmer to edit.")
        End If
    End Sub

    Private Sub DeleteFarmer()
        If dgv.SelectedRows.Count > 0 Then
            Dim id = dgv.SelectedRows(0).Cells("id").Value.ToString()
            Dim name = dgv.SelectedRows(0).Cells("full_name").Value.ToString()
            If MessageBox.Show("Are you sure you want to delete farmer '" & name & "'?", "Confirm", MessageBoxButtons.YesNo) = DialogResult.Yes Then
                Try
                    db.ExecuteNonQuery("DELETE FROM farmers WHERE id = @id", New Dictionary(Of String, Object) From {{"@id", id}})
                    LoadData()
                Catch ex As Exception
                    MessageBox.Show("Error deleting farmer: " & ex.Message)
                End Try
            End If
        Else
            MessageBox.Show("Please select a farmer to delete.")
        End If
    End Sub

    Private Sub PrintData()
        Dim printer As New UIHelper.GridPrinter(dgv, "Farmers Report")
        printer.Print()
    End Sub

    Private Sub ExportData()
        UIHelper.ExportGridToExcel(dgv, "Farmers.xls")
    End Sub
End Class
