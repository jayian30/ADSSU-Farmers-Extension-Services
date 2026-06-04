Imports System.Drawing
Imports System.Windows.Forms

Public Class UsersControl
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
    Private cmbMonth As ComboBox
    Private cmbYear As ComboBox

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
        btnDelete = New Button() With {.Text = "Delete User", .Width = 100, .Height = 30, .Top = 10, .Left = 330}
        btnPrint = New Button() With {.Text = "Print", .Width = 100, .Height = 30, .Top = 10, .Left = 440}
        btnExport = New Button() With {.Text = "Export", .Width = 100, .Height = 30, .Top = 10, .Left = 550}
        
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

        AddHandler btnAdd.Click, AddressOf AddUser
        AddHandler btnEdit.Click, AddressOf EditUser
        AddHandler btnRefresh.Click, AddressOf LoadData
        AddHandler btnDelete.Click, AddressOf DeleteUser
        AddHandler btnPrint.Click, AddressOf PrintData
        AddHandler btnExport.Click, AddressOf ExportData

        pnlHeader.Controls.Add(btnAdd)
        pnlHeader.Controls.Add(btnEdit)
        pnlHeader.Controls.Add(btnRefresh)
        pnlHeader.Controls.Add(btnDelete)
        pnlHeader.Controls.Add(btnPrint)
        pnlHeader.Controls.Add(btnExport)
        
        lblFilter = New Label() With {.Text = "Filter:", .Top = 15, .Left = 670, .Width = 40}
        
        cmbMonth = New ComboBox() With {.Top = 12, .Left = 710, .Width = 90, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbMonth.Items.Add("All Months")
        For i As Integer = 1 To 12
            cmbMonth.Items.Add(MonthName(i))
        Next
        cmbMonth.SelectedIndex = 0
        
        cmbYear = New ComboBox() With {.Top = 12, .Left = 810, .Width = 70, .DropDownStyle = ComboBoxStyle.DropDownList}
        cmbYear.Items.Add("All Years")
        For i As Integer = Date.Now.Year To Date.Now.Year - 5 Step -1
            cmbYear.Items.Add(i.ToString())
        Next
        cmbYear.SelectedIndex = 0
        
        AddHandler cmbMonth.SelectedIndexChanged, AddressOf LoadData
        AddHandler cmbYear.SelectedIndexChanged, AddressOf LoadData
        
        pnlHeader.Controls.Add(lblFilter)
        pnlHeader.Controls.Add(cmbMonth)
        pnlHeader.Controls.Add(cmbYear)

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
            Dim query As String = "SELECT id, username, full_name, role, email, status, created_at FROM users WHERE 1=1"
            Dim parameters As New Dictionary(Of String, Object)
            
            If cmbMonth IsNot Nothing AndAlso cmbMonth.SelectedIndex > 0 Then
                query &= " AND MONTH(created_at) = @month"
                parameters.Add("@month", cmbMonth.SelectedIndex)
            End If
            
            If cmbYear IsNot Nothing AndAlso cmbYear.SelectedIndex > 0 Then
                query &= " AND YEAR(created_at) = @year"
                parameters.Add("@year", cmbYear.SelectedItem.ToString())
            End If
            
            Dim dt = db.ExecuteQuery(query, parameters)
            dgv.DataSource = dt
        Catch ex As Exception
            MessageBox.Show("Error loading users: " & ex.Message)
        End Try
    End Sub

    Private Sub AddUser()
        Dim dlg As New UserDialog()
        If dlg.ShowDialog() = DialogResult.OK Then
            LoadData()
        End If
    End Sub

    Private Sub EditUser()
        If dgv.SelectedRows.Count > 0 Then
            Dim id = dgv.SelectedRows(0).Cells("id").Value.ToString()
            Dim dlg As New UserDialog()
            dlg.LoadData(id)
            If dlg.ShowDialog() = DialogResult.OK Then
                LoadData()
            End If
        Else
            MessageBox.Show("Please select a user to edit.")
        End If
    End Sub

    Private Sub DeleteUser()
        If dgv.SelectedRows.Count > 0 Then
            Dim id = dgv.SelectedRows(0).Cells("id").Value.ToString()
            Dim username = dgv.SelectedRows(0).Cells("username").Value.ToString()
            If MessageBox.Show("Are you sure you want to delete user '" & username & "'?", "Confirm", MessageBoxButtons.YesNo) = DialogResult.Yes Then
                Try
                    db.ExecuteNonQuery("DELETE FROM users WHERE id = @id", New Dictionary(Of String, Object) From {{"@id", id}})
                    LoadData()
                Catch ex As Exception
                    MessageBox.Show("Error deleting user: " & ex.Message)
                End Try
            End If
        Else
            MessageBox.Show("Please select a user to delete.")
        End If
    End Sub

    Private Sub PrintData()
        Dim printer As New UIHelper.GridPrinter(dgv, "System Users Report")
        printer.Print()
    End Sub

    Private Sub ExportData()
        UIHelper.ExportGridToExcel(dgv, "Users.xls")
    End Sub
End Class
