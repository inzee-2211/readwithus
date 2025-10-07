<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php $this->includeTemplate('teacher-request/_partial/header.php', ['siteLangId' => $siteLangId]); ?>
<section class="page-block min-height-500 section--registration">
    <div class="container container--narrow">
        <div class="page-block__cover" id="main-container"></div>
    </div>
</section>
<?php $this->includeTemplate('teacher-request/_partial/footer.php', ['siteLangId' => $siteLangId]); ?>
<script>
    $(document).ready(function() {
        getform(<?php echo $step; ?>);
    });
</script>