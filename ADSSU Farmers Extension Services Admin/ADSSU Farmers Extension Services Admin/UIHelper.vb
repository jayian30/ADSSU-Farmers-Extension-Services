Imports System.Drawing
Imports System.Windows.Forms

Public Class UIHelper
    Public Shared Sub StyleGrid(dgv As DataGridView)
        dgv.BackgroundColor = Color.White
        dgv.BorderStyle = BorderStyle.None
        dgv.CellBorderStyle = DataGridViewCellBorderStyle.SingleHorizontal
        dgv.ColumnHeadersBorderStyle = DataGridViewHeaderBorderStyle.None
        dgv.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill
        
        ' Header style
        dgv.EnableHeadersVisualStyles = False
        dgv.ColumnHeadersDefaultCellStyle.BackColor = Color.FromArgb(46, 125, 50) ' Dark Green
        dgv.ColumnHeadersDefaultCellStyle.ForeColor = Color.White
        dgv.ColumnHeadersDefaultCellStyle.Font = New Font("Segoe UI", 10, FontStyle.Bold)
        dgv.ColumnHeadersDefaultCellStyle.SelectionBackColor = Color.FromArgb(46, 125, 50)
        dgv.ColumnHeadersHeight = 40
        
        ' Row style
        dgv.DefaultCellStyle.SelectionBackColor = Color.FromArgb(224, 242, 241) ' Light green tint
        dgv.DefaultCellStyle.SelectionForeColor = Color.FromArgb(33, 33, 33)
        dgv.DefaultCellStyle.Font = New Font("Segoe UI", 10)
        dgv.DefaultCellStyle.Padding = New Padding(5)
        dgv.RowTemplate.Height = 35
        
        ' Alternating row colors
        dgv.AlternatingRowsDefaultCellStyle.BackColor = Color.FromArgb(249, 250, 251)
        
        ' Remove row headers
        dgv.RowHeadersVisible = False
        dgv.SelectionMode = DataGridViewSelectionMode.FullRowSelect
    End Sub

    Public Shared Sub StylePrimaryButton(btn As Button)
        btn.FlatStyle = FlatStyle.Flat
        btn.FlatAppearance.BorderSize = 0
        btn.BackColor = Color.FromArgb(33, 150, 243) ' Blue
        btn.ForeColor = Color.White
        btn.Font = New Font("Segoe UI", 9.5F, FontStyle.Bold)
        btn.Cursor = Cursors.Hand
    End Sub

    Public Shared Sub StyleDangerButton(btn As Button)
        btn.FlatStyle = FlatStyle.Flat
        btn.FlatAppearance.BorderSize = 0
        btn.BackColor = Color.FromArgb(244, 67, 54) ' Red
        btn.ForeColor = Color.White
        btn.Font = New Font("Segoe UI", 9.5F, FontStyle.Bold)
        btn.Cursor = Cursors.Hand
    End Sub

    Public Shared Sub ExportGridToExcel(dgv As DataGridView, defaultFileName As String)
        Dim sfd As New SaveFileDialog()
        sfd.Filter = "Excel File|*.xls"
        sfd.FileName = defaultFileName
        If sfd.ShowDialog() = DialogResult.OK Then
            Try
                Dim sb As New System.Text.StringBuilder()
                sb.AppendLine("<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>")
                sb.AppendLine("<head>")
                sb.AppendLine("<style>")
                sb.AppendLine("  .header { background-color: #2E7D32; color: white; font-weight: bold; text-align: center; border: 1px solid #000; padding: 5px; }")
                sb.AppendLine("  .cell { border: 1px solid #ccc; padding: 3px; mso-number-format:""\@""; }")
                sb.AppendLine("</style>")
                sb.AppendLine("</head>")
                sb.AppendLine("<body>")
                sb.AppendLine("<table style='border-collapse: collapse;'>")
                
                sb.AppendLine("  <tr>")
                Dim headers = dgv.Columns.Cast(Of DataGridViewColumn)().Where(Function(c) c.Visible).Select(Function(c) Globalization.CultureInfo.CurrentCulture.TextInfo.ToTitleCase(c.HeaderText.Replace("_", " ").ToLower()))
                For Each header In headers
                    sb.AppendLine("    <td class='header'>" & System.Net.WebUtility.HtmlEncode(header) & "</td>")
                Next
                sb.AppendLine("  </tr>")
                
                For Each row As DataGridViewRow In dgv.Rows
                    If Not row.IsNewRow Then
                        sb.AppendLine("  <tr>")
                        For Each c As DataGridViewColumn In dgv.Columns
                            If c.Visible Then
                                Dim valObj = row.Cells(c.Index).Value
                                Dim cellText As String = ""
                                If valObj IsNot Nothing Then
                                    If TypeOf valObj Is Date Then
                                        cellText = CDate(valObj).ToString("yyyy-MM-dd")
                                    Else
                                        cellText = valObj.ToString()
                                    End If
                                End If
                                sb.AppendLine("    <td class='cell'>" & System.Net.WebUtility.HtmlEncode(cellText) & "</td>")
                            End If
                        Next
                        sb.AppendLine("  </tr>")
                    End If
                Next
                sb.AppendLine("</table>")
                sb.AppendLine("</body>")
                sb.AppendLine("</html>")
                
                System.IO.File.WriteAllText(sfd.FileName, sb.ToString())
                MessageBox.Show("Exported successfully.", "Success", MessageBoxButtons.OK, MessageBoxIcon.Information)
            Catch ex As Exception
                MessageBox.Show("Error exporting: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If
    End Sub

    Public Class GridPrinter
        Private dgv As DataGridView
        Private printRowIndex As Integer = 0
        Private title As String

        Public Sub New(grid As DataGridView, reportTitle As String)
            dgv = grid
            title = reportTitle
        End Sub

        Public Sub Print()
            Dim printDoc As New System.Drawing.Printing.PrintDocument()
            printDoc.DefaultPageSettings.Landscape = True
            printDoc.DefaultPageSettings.Margins = New System.Drawing.Printing.Margins(50, 50, 50, 50)
            AddHandler printDoc.PrintPage, AddressOf PrintPage
            Dim ppd As New PrintPreviewDialog()
            ppd.Document = printDoc
            ppd.WindowState = FormWindowState.Maximized
            ppd.ShowDialog()
        End Sub

        Private Sub PrintPage(sender As Object, e As System.Drawing.Printing.PrintPageEventArgs)
            Dim font As New Font("Segoe UI", 9)
            Dim boldFont As New Font("Segoe UI", 9, FontStyle.Bold)
            Dim y As Integer = e.MarginBounds.Top
            Dim x As Integer = e.MarginBounds.Left
            
            If printRowIndex = 0 Then
                e.Graphics.DrawString(title, New Font("Segoe UI", 16, FontStyle.Bold), Brushes.Black, New Point(x, y))
                y += 40
            End If
            
            Dim maxRowHeight As Integer = 35
            
            Dim totalGridWidth As Integer = 0
            For Each col As DataGridViewColumn In dgv.Columns
                If col.Visible Then totalGridWidth += col.Width
            Next
            
            Dim availableWidth As Integer = e.MarginBounds.Width
            Dim colWidths As New Dictionary(Of Integer, Integer)
            For Each col As DataGridViewColumn In dgv.Columns
                If col.Visible Then
                    colWidths(col.Index) = CInt((col.Width / totalGridWidth) * availableWidth)
                End If
            Next
            
            x = e.MarginBounds.Left
            For Each col As DataGridViewColumn In dgv.Columns
                If col.Visible Then
                    Dim colWidth = colWidths(col.Index)
                    Dim rect As New Rectangle(x, y, colWidth, maxRowHeight)
                    e.Graphics.FillRectangle(Brushes.LightGray, rect)
                    e.Graphics.DrawRectangle(Pens.Black, rect)
                    
                    Dim headerText = Globalization.CultureInfo.CurrentCulture.TextInfo.ToTitleCase(col.HeaderText.Replace("_", " ").ToLower())
                    e.Graphics.DrawString(headerText, boldFont, Brushes.Black, rect, New StringFormat() With {.LineAlignment = StringAlignment.Center, .Alignment = StringAlignment.Center})
                    x += colWidth
                End If
            Next
            y += maxRowHeight
            
            While printRowIndex < dgv.Rows.Count
                Dim row = dgv.Rows(printRowIndex)
                If Not row.IsNewRow Then
                    x = e.MarginBounds.Left
                    For Each col As DataGridViewColumn In dgv.Columns
                        If col.Visible Then
                            Dim colWidth = colWidths(col.Index)
                            Dim valObj = row.Cells(col.Index).Value
                            Dim valStr As String = ""
                            
                            If valObj IsNot Nothing Then
                                If TypeOf valObj Is Date Then
                                    valStr = CDate(valObj).ToString("yyyy-MM-dd")
                                Else
                                    valStr = valObj.ToString()
                                End If
                            End If
                            
                            Dim rect As New Rectangle(x, y, colWidth, maxRowHeight)
                            e.Graphics.DrawRectangle(Pens.Black, rect)
                            
                            Dim sf As New StringFormat() With {
                                .LineAlignment = StringAlignment.Center,
                                .Trimming = StringTrimming.Word,
                                .FormatFlags = StringFormatFlags.LineLimit
                            }
                            Dim paddedRect As New RectangleF(rect.X + 2, rect.Y + 2, rect.Width - 4, rect.Height - 4)
                            e.Graphics.DrawString(valStr, font, Brushes.Black, paddedRect, sf)
                            
                            x += colWidth
                        End If
                    Next
                    y += maxRowHeight
                    
                    If y + maxRowHeight > e.MarginBounds.Bottom Then
                        printRowIndex += 1
                        e.HasMorePages = True
                        Exit Sub
                    End If
                End If
                printRowIndex += 1
            End While
            
            e.HasMorePages = False
            printRowIndex = 0
        End Sub
    End Class
End Class
