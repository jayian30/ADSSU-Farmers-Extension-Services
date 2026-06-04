Imports System.Data

Public Class DashboardControl
    Inherits UserControl

    Private db As New DatabaseHelper()
    Private lblUsersCount As Label
    Private lblFarmersCount As Label
    Private lblWorkersCount As Label
    Private lblProgramsCount As Label
    
    Private pnlChart As Panel
    Private usersVal As Integer = 0
    Private farmersVal As Integer = 0
    Private workersVal As Integer = 0
    Private programsVal As Integer = 0

    Public Sub New()
        Me.BackColor = Color.White
        Me.Padding = New Padding(20)

        ' Setup UI
        Dim flp As New FlowLayoutPanel()
        flp.Dock = DockStyle.Top
        flp.Height = 150
        flp.AutoScroll = True
        Me.Controls.Add(flp)

        lblUsersCount = CreateCard(flp, "Total Users", Color.FromArgb(33, 150, 243))
        lblFarmersCount = CreateCard(flp, "Registered Farmers", Color.FromArgb(76, 175, 80))
        lblWorkersCount = CreateCard(flp, "Extension Workers", Color.FromArgb(255, 152, 0))
        lblProgramsCount = CreateCard(flp, "Active Programs", Color.FromArgb(156, 39, 176))
        
        ' Chart Panel
        pnlChart = New Panel()
        pnlChart.Dock = DockStyle.Fill
        pnlChart.Padding = New Padding(20)
        pnlChart.BackColor = Color.White
        AddHandler pnlChart.Paint, AddressOf DrawChart
        AddHandler pnlChart.Resize, Sub(s, e) pnlChart.Invalidate()
        Me.Controls.Add(pnlChart)
        pnlChart.BringToFront()
    End Sub

    Private Function CreateCard(parent As Control, title As String, bgColor As Color) As Label
        Dim panel As New Panel()
        panel.Size = New Size(220, 120)
        panel.BackColor = bgColor
        panel.Margin = New Padding(10)

        Dim lblTitle As New Label()
        lblTitle.Text = title
        lblTitle.ForeColor = Color.White
        lblTitle.Font = New Font("Segoe UI", 12, FontStyle.Bold)
        lblTitle.Dock = DockStyle.Top
        lblTitle.Height = 30
        lblTitle.TextAlign = ContentAlignment.MiddleCenter
        panel.Controls.Add(lblTitle)

        Dim lblValue As New Label()
        lblValue.Text = "0"
        lblValue.ForeColor = Color.White
        lblValue.Font = New Font("Segoe UI", 24, FontStyle.Bold)
        lblValue.Dock = DockStyle.Fill
        lblValue.TextAlign = ContentAlignment.MiddleCenter
        panel.Controls.Add(lblValue)

        parent.Controls.Add(panel)
        Return lblValue
    End Function
    
    Private Sub DrawChart(sender As Object, e As PaintEventArgs)
        Dim g As Graphics = e.Graphics
        g.SmoothingMode = Drawing2D.SmoothingMode.AntiAlias
        
        Dim titleFont As New Font("Segoe UI", 16, FontStyle.Bold)
        g.DrawString("Population Overview Chart", titleFont, Brushes.Black, 20, 20)
        
        Dim maxVal As Integer = Math.Max(Math.Max(usersVal, farmersVal), Math.Max(workersVal, programsVal))
        If maxVal = 0 Then maxVal = 1
        
        Dim barWidth As Integer = 120
        Dim spacing As Integer = 80
        Dim startX As Integer = 50
        Dim bottomY As Integer = pnlChart.Height - 50
        Dim maxHeight As Integer = pnlChart.Height - 150
        
        If maxHeight < 50 Then Return ' Prevent drawing if too small
        
        Dim drawBar = Sub(val As Integer, title As String, color As Color, xPos As Integer)
                          Dim h As Integer = CInt((val / maxVal) * maxHeight)
                          If h < 5 Then h = 5
                          Dim rect As New Rectangle(xPos, bottomY - h, barWidth, h)
                          Using b As New SolidBrush(color)
                              g.FillRectangle(b, rect)
                          End Using
                          
                          Dim f As New Font("Segoe UI", 10, FontStyle.Bold)
                          Dim sz = g.MeasureString(title, f)
                          g.DrawString(title, f, Brushes.Black, xPos + (barWidth - sz.Width) / 2, bottomY + 10)
                          
                          Dim valF As New Font("Segoe UI", 12, FontStyle.Bold)
                          Dim valSz = g.MeasureString(val.ToString(), valF)
                          g.DrawString(val.ToString(), valF, Brushes.Black, xPos + (barWidth - valSz.Width) / 2, bottomY - h - 25)
                      End Sub
                      
        drawBar(usersVal, "Users", Color.FromArgb(33, 150, 243), startX)
        drawBar(farmersVal, "Farmers", Color.FromArgb(76, 175, 80), startX + barWidth + spacing)
        drawBar(workersVal, "Workers", Color.FromArgb(255, 152, 0), startX + (barWidth + spacing) * 2)
        drawBar(programsVal, "Programs", Color.FromArgb(156, 39, 176), startX + (barWidth + spacing) * 3)
    End Sub

    Public Sub LoadData()
        Try
            Dim objUsers = db.ExecuteScalar("SELECT COUNT(*) FROM users")
            usersVal = If(objUsers IsNot Nothing, Convert.ToInt32(objUsers), 0)
            lblUsersCount.Text = usersVal.ToString()

            Dim objFarmers = db.ExecuteScalar("SELECT COUNT(*) FROM farmers")
            farmersVal = If(objFarmers IsNot Nothing, Convert.ToInt32(objFarmers), 0)
            lblFarmersCount.Text = farmersVal.ToString()

            Dim objWorkers = db.ExecuteScalar("SELECT COUNT(*) FROM extension_workers")
            workersVal = If(objWorkers IsNot Nothing, Convert.ToInt32(objWorkers), 0)
            lblWorkersCount.Text = workersVal.ToString()

            Dim objPrograms = db.ExecuteScalar("SELECT COUNT(*) FROM agricultural_programs WHERE status='active'")
            programsVal = If(objPrograms IsNot Nothing, Convert.ToInt32(objPrograms), 0)
            lblProgramsCount.Text = programsVal.ToString()
            
            pnlChart.Invalidate()
        Catch ex As Exception
            MessageBox.Show("Error loading dashboard data: " & ex.Message)
        End Try
    End Sub
End Class
