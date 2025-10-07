<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
if ($lecture) {
    if (!empty($video['lecsrc_link'])) { ?>
        <div class="course-video ratio ratio--2by1 ">
            <div id="YTPlayerJs"></div>
            <?php
            $videoId = YouTube::getVideoId($video['lecsrc_link']);
            ?>
            <script>
                var player;

                function onYouTubePlayerAPIReady() {
                    player = new YT.Player('YTPlayerJs', {
                        width: '100%',
                        height: '100%',
                        videoId: "<?php echo $videoId ?>",
                        events: {
                            onReady: onPlayerReady,
                            onStateChange: onPlayerStateChange
                        }
                    });
                }

                function onPlayerReady(event) {
                    event.target.playVideo();
                }

                function onPlayerStateChange(event) {
                    if (event.data === 0) {
                        getLecture(1);
                    }
                }
                $(document).ready(function() {
                    onYouTubePlayerAPIReady();
                });
            </script>
        </div>
        <div class="directions">
            <?php if ($previousLecture) { ?>
                <a href="javascript:void(0)" class="directions-prev getPrevJs">
                    <span class="directions-title">
                        <?php echo $previousLecture['lecture_order'] . '. ' . $previousLecture['lecture_title']; ?>
                    </span>
                    <span href="javascript:void(0)" class="directions-prev__control"></span>
                </a>
            <?php } ?>
            <?php if ($nextLecture) { ?>
                <a href="javascript:void(0)" class="directions-next getNextJs">
                    <span class="directions-title">
                        <?php echo $nextLecture['lecture_order'] . '. ' . $nextLecture['lecture_title']; ?>
                    </span>
                    <span href="javascript:void(0)" class="directions-next__control"></span>
                </a>
            <?php } ?>
        </div>
        <?php
    }
    ?>
    <script>
        $(document).ready(function() {
            var isLast = "<?php echo $lecture['lecture_order']; ?>";
            $('.getPrevJs, .getNextJs').attr('last-record', 0);
            if (isLast <= 1) {
                $('.getPrevJs').attr('last-record', 1);
            }
        });
    </script><?php
            } else { ?>
    <script>
        $(document).ready(function() {
            $('.getNextJs').attr('last-record', 1);
        });
    </script> <?php
            }
