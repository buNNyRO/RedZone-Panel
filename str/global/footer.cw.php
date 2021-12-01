				</div>
			</div>
	  	</div>

          <footer class="footer" style="font-size:12px;"> Made for RED-ZONE.RO <span class="float-right">Copyright @ <a href="https://consty.ro/">consty</a></span> [took <?php $end_time = microtime(TRUE); $time_taken = $end_time - $start_time; $time_taken = round($time_taken, 3); echo $time_taken; ?> seconds to load]</footer>
    </div>

    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/js/main.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/popper/popper.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/dist/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/dist/js/waves.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/dist/js/sidebarmenu.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/dist/js/custom.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/dist/js/change-theme.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/raphael/raphael-min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/morrisjs/morris.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/jquery-sparkline/jquery.sparkline.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/toast-master/js/jquery.toast.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/peity/jquery.peity.min.js"></script>
    <script src="<?php echo this::$_PAGE_URL ?>resources/assets/node_modules/peity/jquery.peity.init.js"></script>
</body>

</html>

<?php
ob_flush();
?>