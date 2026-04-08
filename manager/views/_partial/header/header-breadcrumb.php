<?php defined('SYSTEM_INIT') or die('Invalid usage'); ?>
<ul class="breadcrumb ">
    <li><a href="<?php echo MyUtility::makeUrl('') ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
    <?php
    if (!empty($this->variables['nodes'])) {
        foreach ($this->variables['nodes'] as $nodes) {
            ?>
            <?php if (!empty($nodes['href'])) { ?>
                <li>
                    <a href="<?php echo $nodes['href']; ?>" <?php echo (!empty($nodes['other'])) ? $nodes['other'] : ''; ?>>
                        <?php
                        $title = str_replace(' ', '_', $nodes['title']);
                        echo Label::getLabel('LBL_' . $title);
                        ?>
                    </a>
                </li>
            <?php } else { ?>
                <li>
                    <?php
                    $title = str_replace(' ', '_', $nodes['title']);
                    echo (isset($nodes['title'])) ? Label::getLabel('LBL_' . $title) : '';
                    ?>
                </li>
                <?php
            }
        }
    }
    ?>
</ul>