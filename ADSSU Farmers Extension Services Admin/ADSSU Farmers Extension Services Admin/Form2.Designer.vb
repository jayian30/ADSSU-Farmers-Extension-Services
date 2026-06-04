<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class Form2
    Inherits System.Windows.Forms.Form

    <System.Diagnostics.DebuggerNonUserCode()>
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    Private components As System.ComponentModel.IContainer

    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Me.pnlSidebar = New System.Windows.Forms.Panel()
        Me.btnPrograms = New System.Windows.Forms.Button()
        Me.btnWorkers = New System.Windows.Forms.Button()
        Me.btnFarmers = New System.Windows.Forms.Button()
        Me.btnUsers = New System.Windows.Forms.Button()
        Me.btnDashboard = New System.Windows.Forms.Button()
        Me.pnlLogo = New System.Windows.Forms.Panel()
        Me.lblLogo = New System.Windows.Forms.Label()
        Me.btnLogout = New System.Windows.Forms.Button()
        Me.pnlHeader = New System.Windows.Forms.Panel()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.pnlContent = New System.Windows.Forms.Panel()
        Me.pnlSidebar.SuspendLayout()
        Me.pnlLogo.SuspendLayout()
        Me.pnlHeader.SuspendLayout()
        Me.SuspendLayout()
        '
        'pnlSidebar
        '
        Me.pnlSidebar.BackColor = System.Drawing.Color.FromArgb(CType(CType(33, Byte), Integer), CType(CType(33, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.pnlSidebar.Controls.Add(Me.btnPrograms)
        Me.pnlSidebar.Controls.Add(Me.btnWorkers)
        Me.pnlSidebar.Controls.Add(Me.btnFarmers)
        Me.pnlSidebar.Controls.Add(Me.btnUsers)
        Me.pnlSidebar.Controls.Add(Me.btnDashboard)
        Me.pnlSidebar.Controls.Add(Me.btnLogout)
        Me.pnlSidebar.Controls.Add(Me.pnlLogo)
        Me.pnlSidebar.Dock = System.Windows.Forms.DockStyle.Left
        Me.pnlSidebar.Location = New System.Drawing.Point(0, 0)
        Me.pnlSidebar.Name = "pnlSidebar"
        Me.pnlSidebar.Size = New System.Drawing.Size(250, 720)
        Me.pnlSidebar.TabIndex = 0
        '
        'pnlLogo
        '
        Me.pnlLogo.BackColor = System.Drawing.Color.FromArgb(CType(CType(27, Byte), Integer), CType(CType(94, Byte), Integer), CType(CType(32, Byte), Integer))
        Me.pnlLogo.Controls.Add(Me.lblLogo)
        Me.pnlLogo.Dock = System.Windows.Forms.DockStyle.Top
        Me.pnlLogo.Location = New System.Drawing.Point(0, 0)
        Me.pnlLogo.Name = "pnlLogo"
        Me.pnlLogo.Size = New System.Drawing.Size(250, 80)
        Me.pnlLogo.TabIndex = 0
        '
        'lblLogo
        '
        Me.lblLogo.Dock = System.Windows.Forms.DockStyle.Fill
        Me.lblLogo.Font = New System.Drawing.Font("Segoe UI", 14.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point)
        Me.lblLogo.ForeColor = System.Drawing.Color.White
        Me.lblLogo.Location = New System.Drawing.Point(0, 0)
        Me.lblLogo.Name = "lblLogo"
        Me.lblLogo.Size = New System.Drawing.Size(250, 80)
        Me.lblLogo.TabIndex = 0
        Me.lblLogo.Text = "ADSSU Admin"
        Me.lblLogo.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'btnDashboard
        '
        Me.btnDashboard.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnDashboard.FlatAppearance.BorderSize = 0
        Me.btnDashboard.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnDashboard.Font = New System.Drawing.Font("Segoe UI", 11.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point)
        Me.btnDashboard.ForeColor = System.Drawing.Color.LightGray
        Me.btnDashboard.Location = New System.Drawing.Point(0, 80)
        Me.btnDashboard.Name = "btnDashboard"
        Me.btnDashboard.Padding = New System.Windows.Forms.Padding(20, 0, 0, 0)
        Me.btnDashboard.Size = New System.Drawing.Size(250, 60)
        Me.btnDashboard.TabIndex = 1
        Me.btnDashboard.Text = "Dashboard"
        Me.btnDashboard.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDashboard.UseVisualStyleBackColor = True
        '
        'btnUsers
        '
        Me.btnUsers.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnUsers.FlatAppearance.BorderSize = 0
        Me.btnUsers.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnUsers.Font = New System.Drawing.Font("Segoe UI", 11.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point)
        Me.btnUsers.ForeColor = System.Drawing.Color.LightGray
        Me.btnUsers.Location = New System.Drawing.Point(0, 140)
        Me.btnUsers.Name = "btnUsers"
        Me.btnUsers.Padding = New System.Windows.Forms.Padding(20, 0, 0, 0)
        Me.btnUsers.Size = New System.Drawing.Size(250, 60)
        Me.btnUsers.TabIndex = 2
        Me.btnUsers.Text = "Manage Users"
        Me.btnUsers.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnUsers.UseVisualStyleBackColor = True
        '
        'btnFarmers
        '
        Me.btnFarmers.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnFarmers.FlatAppearance.BorderSize = 0
        Me.btnFarmers.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnFarmers.Font = New System.Drawing.Font("Segoe UI", 11.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point)
        Me.btnFarmers.ForeColor = System.Drawing.Color.LightGray
        Me.btnFarmers.Location = New System.Drawing.Point(0, 200)
        Me.btnFarmers.Name = "btnFarmers"
        Me.btnFarmers.Padding = New System.Windows.Forms.Padding(20, 0, 0, 0)
        Me.btnFarmers.Size = New System.Drawing.Size(250, 60)
        Me.btnFarmers.TabIndex = 3
        Me.btnFarmers.Text = "Farmers"
        Me.btnFarmers.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnFarmers.UseVisualStyleBackColor = True
        '
        'btnWorkers
        '
        Me.btnWorkers.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnWorkers.FlatAppearance.BorderSize = 0
        Me.btnWorkers.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnWorkers.Font = New System.Drawing.Font("Segoe UI", 11.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point)
        Me.btnWorkers.ForeColor = System.Drawing.Color.LightGray
        Me.btnWorkers.Location = New System.Drawing.Point(0, 260)
        Me.btnWorkers.Name = "btnWorkers"
        Me.btnWorkers.Padding = New System.Windows.Forms.Padding(20, 0, 0, 0)
        Me.btnWorkers.Size = New System.Drawing.Size(250, 60)
        Me.btnWorkers.TabIndex = 4
        Me.btnWorkers.Text = "Extension Workers"
        Me.btnWorkers.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnWorkers.UseVisualStyleBackColor = True
        '
        'btnPrograms
        '
        Me.btnPrograms.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnPrograms.FlatAppearance.BorderSize = 0
        Me.btnPrograms.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnPrograms.Font = New System.Drawing.Font("Segoe UI", 11.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point)
        Me.btnPrograms.ForeColor = System.Drawing.Color.LightGray
        Me.btnPrograms.Location = New System.Drawing.Point(0, 320)
        Me.btnPrograms.Name = "btnPrograms"
        Me.btnPrograms.Padding = New System.Windows.Forms.Padding(20, 0, 0, 0)
        Me.btnPrograms.Size = New System.Drawing.Size(250, 60)
        Me.btnPrograms.TabIndex = 5
        Me.btnPrograms.Text = "Programs"
        Me.btnPrograms.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnPrograms.UseVisualStyleBackColor = True
        '
        'btnLogout
        '
        Me.btnLogout.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.btnLogout.FlatAppearance.BorderSize = 0
        Me.btnLogout.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnLogout.Font = New System.Drawing.Font("Segoe UI", 11.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point)
        Me.btnLogout.ForeColor = System.Drawing.Color.LightGray
        Me.btnLogout.Location = New System.Drawing.Point(0, 660)
        Me.btnLogout.Name = "btnLogout"
        Me.btnLogout.Padding = New System.Windows.Forms.Padding(20, 0, 0, 0)
        Me.btnLogout.Size = New System.Drawing.Size(250, 60)
        Me.btnLogout.TabIndex = 6
        Me.btnLogout.Text = "Logout"
        Me.btnLogout.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnLogout.UseVisualStyleBackColor = True
        '
        'pnlHeader
        '
        Me.pnlHeader.BackColor = System.Drawing.Color.White
        Me.pnlHeader.Controls.Add(Me.lblTitle)
        Me.pnlHeader.Dock = System.Windows.Forms.DockStyle.Top
        Me.pnlHeader.Location = New System.Drawing.Point(250, 0)
        Me.pnlHeader.Name = "pnlHeader"
        Me.pnlHeader.Size = New System.Drawing.Size(1030, 80)
        Me.pnlHeader.TabIndex = 1
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 16.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point)
        Me.lblTitle.ForeColor = System.Drawing.Color.FromArgb(CType(CType(64, Byte), Integer), CType(CType(64, Byte), Integer), CType(CType(64, Byte), Integer))
        Me.lblTitle.Location = New System.Drawing.Point(30, 25)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(125, 30)
        Me.lblTitle.TabIndex = 0
        Me.lblTitle.Text = "Dashboard"
        '
        'pnlContent
        '
        Me.pnlContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.pnlContent.Location = New System.Drawing.Point(250, 80)
        Me.pnlContent.Name = "pnlContent"
        Me.pnlContent.Size = New System.Drawing.Size(1030, 640)
        Me.pnlContent.TabIndex = 2
        '
        'Form2
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(7.0!, 15.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.FromArgb(CType(CType(245, Byte), Integer), CType(CType(246, Byte), Integer), CType(CType(250, Byte), Integer))
        Me.ClientSize = New System.Drawing.Size(1280, 720)
        Me.Controls.Add(Me.pnlContent)
        Me.Controls.Add(Me.pnlHeader)
        Me.Controls.Add(Me.pnlSidebar)
        Me.MinimumSize = New System.Drawing.Size(800, 600)
        Me.Name = "Form2"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "ADSSU Farmers Extension - Dashboard"
        Me.pnlSidebar.ResumeLayout(False)
        Me.pnlLogo.ResumeLayout(False)
        Me.pnlHeader.ResumeLayout(False)
        Me.pnlHeader.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents pnlSidebar As Panel
    Friend WithEvents pnlLogo As Panel
    Friend WithEvents lblLogo As Label
    Friend WithEvents btnPrograms As Button
    Friend WithEvents btnWorkers As Button
    Friend WithEvents btnFarmers As Button
    Friend WithEvents btnUsers As Button
    Friend WithEvents btnDashboard As Button
    Friend WithEvents btnLogout As Button
    Friend WithEvents pnlHeader As Panel
    Friend WithEvents lblTitle As Label
    Friend WithEvents pnlContent As Panel
End Class
