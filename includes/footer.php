<?php
// includes/footer.php
$base_url = (getenv('PORT') !== false || getenv('RAILWAY_STATIC_URL') !== false) ? "" : "/ADSSU Farmers Extension Services";
?>
    </div> <!-- End content-wrapper -->
</section> <!-- End main-content -->

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom JS -->
<script src="<?php echo $base_url; ?>/assets/js/main.js"></script>

</body>
</html>
