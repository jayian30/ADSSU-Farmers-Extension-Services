Public Class Form2
    ' Instances of UserControls
    Private dashboardCtrl As New DashboardControl()
    Private usersCtrl As New UsersControl()
    Private farmersCtrl As New FarmersControl()
    Private workersCtrl As New WorkersControl()
    Private programsCtrl As New ProgramsControl()

    Private Sub ShowControl(ctrl As UserControl, title As String)
        dashboardCtrl.Visible = False
        usersCtrl.Visible = False
        farmersCtrl.Visible = False
        workersCtrl.Visible = False
        programsCtrl.Visible = False

        ctrl.Visible = True
        lblTitle.Text = title
    End Sub

    Private Sub SetActiveButton(activeBtn As Button)
        ' Reset all
        btnDashboard.BackColor = Color.FromArgb(33, 33, 33)
        btnUsers.BackColor = Color.FromArgb(33, 33, 33)
        btnFarmers.BackColor = Color.FromArgb(33, 33, 33)
        btnWorkers.BackColor = Color.FromArgb(33, 33, 33)
        btnPrograms.BackColor = Color.FromArgb(33, 33, 33)
        
        ' Set active
        activeBtn.BackColor = Color.FromArgb(46, 125, 50) ' Dark Green highlight
    End Sub

    Private Sub Form2_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        ' Setup UserControls
        dashboardCtrl.Dock = DockStyle.Fill
        usersCtrl.Dock = DockStyle.Fill
        farmersCtrl.Dock = DockStyle.Fill
        workersCtrl.Dock = DockStyle.Fill
        programsCtrl.Dock = DockStyle.Fill

        ' Add to panel and hide all except Dashboard
        pnlContent.Controls.Add(dashboardCtrl)
        pnlContent.Controls.Add(usersCtrl)
        pnlContent.Controls.Add(farmersCtrl)
        pnlContent.Controls.Add(workersCtrl)
        pnlContent.Controls.Add(programsCtrl)

        ' Set cursor to hand for all buttons
        For Each ctrl As Control In pnlSidebar.Controls
            If TypeOf ctrl Is Button Then
                ctrl.Cursor = Cursors.Hand
            End If
        Next

        ShowControl(dashboardCtrl, "Dashboard")
        SetActiveButton(btnDashboard)
        dashboardCtrl.LoadData()
    End Sub

    Private Sub Form2_FormClosed(sender As Object, e As FormClosedEventArgs) Handles MyBase.FormClosed
        Application.Exit()
    End Sub

    Private Sub btnDashboard_Click(sender As Object, e As EventArgs) Handles btnDashboard.Click
        ShowControl(dashboardCtrl, "Dashboard")
        SetActiveButton(btnDashboard)
        dashboardCtrl.LoadData()
    End Sub

    Private Sub btnUsers_Click(sender As Object, e As EventArgs) Handles btnUsers.Click
        ShowControl(usersCtrl, "Manage Users")
        SetActiveButton(btnUsers)
        usersCtrl.LoadData()
    End Sub

    Private Sub btnFarmers_Click(sender As Object, e As EventArgs) Handles btnFarmers.Click
        ShowControl(farmersCtrl, "Farmers")
        SetActiveButton(btnFarmers)
        farmersCtrl.LoadData()
    End Sub

    Private Sub btnWorkers_Click(sender As Object, e As EventArgs) Handles btnWorkers.Click
        ShowControl(workersCtrl, "Extension Workers")
        SetActiveButton(btnWorkers)
        workersCtrl.LoadData()
    End Sub

    Private Sub btnPrograms_Click(sender As Object, e As EventArgs) Handles btnPrograms.Click
        ShowControl(programsCtrl, "Programs")
        SetActiveButton(btnPrograms)
        programsCtrl.LoadData()
    End Sub

    Private Sub btnLogout_Click(sender As Object, e As EventArgs) Handles btnLogout.Click
        Dim login As New Form1()
        login.Show()
        Me.Hide()
    End Sub
End Class
