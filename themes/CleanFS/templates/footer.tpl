<?php $this->display('shortcuts.tpl'); ?>

</div>
<p id="footer">
    <!-- Please don't remove this line - it helps promote Flyspray -->
    <a href="http://flyspray.org/" class="offsite"><?php echo Filters::noXSS(L('poweredby')); ?><?php if ($user->perms('is_admin')): ?> <?php echo Filters::noXSS($fs->version); ?> <?php endif; ?></a><br/>
    <i><a href="http://www.thevelozgroup.com"><?php echo Filters::noXSS(L('sponsoredby')); ?> The Veloz Group</a></i>
</p>
<script type="text/javascript">
    $.feedback({
        ajaxURL: 'http://kredytum.ilware.web.rico.egmit/feedback/forward',
        html2canvasURL: '<?php echo Filters::noXSS($baseurl); ?>js/feedback/html2canvas.js',
        postHTML: false
    });
</script>
</body>
</html>
