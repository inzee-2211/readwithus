<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<aside class="rwu-course-sidebar">
  <!-- Course Category -->
  <div class="rwu-filter-block">
    <h3 class="rwu-filter-title"><?php echo Label::getLabel('LBL_COURSE_CATEGORY'); ?></h3>
    <ul class="rwu-filter-list">
      <?php foreach ($category->options as $id => $option): ?>
        <li>
          <label>
            <input type="checkbox" name="course_cate_id[]" 
                   value="<?php echo $id; ?>"
                   <?php echo in_array($id, $category->value) ? 'checked' : ''; ?>>
            <span><?php echo $option['name']; ?></span>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Instructors -->
  <div class="rwu-filter-block">
    <h3 class="rwu-filter-title"><?php echo Label::getLabel('LBL_INSTRUCTORS'); ?></h3>
    <ul class="rwu-filter-list">
      <li><label><input type="checkbox"> <span>Kenny White</span></label></li>
      <li><label><input type="checkbox"> <span>John Doe</span></label></li>
    </ul>
  </div>

  <!-- Price -->
  <div class="rwu-filter-block">
    <h3 class="rwu-filter-title"><?php echo Label::getLabel('LBL_PRICE'); ?></h3>
    <ul class="rwu-filter-list">
      <li><label><input type="checkbox" name="price[]" value="free"> <span>Free</span></label></li>
      <li><label><input type="checkbox" name="price[]" value="paid"> <span>Paid</span></label></li>
    </ul>
  </div>

  <!-- Review -->
  <div class="rwu-filter-block">
    <h3 class="rwu-filter-title"><?php echo Label::getLabel('LBL_REVIEW'); ?></h3>
    <ul class="rwu-filter-list">
      <?php foreach ($ratings->options as $id => $option): ?>
        <li>
          <label>
            <input type="radio" name="course_ratings" value="<?php echo $id; ?>"
                   <?php echo $id == $ratings->value ? 'checked' : ''; ?>>
            <span><?php echo $option; ?></span>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Level -->
  <div class="rwu-filter-block">
    <h3 class="rwu-filter-title"><?php echo Label::getLabel('LBL_LEVEL'); ?></h3>
    <ul class="rwu-filter-list">
      <?php foreach ($level->options as $id => $option): ?>
        <li>
          <label>
            <input type="checkbox" name="course_level[]" value="<?php echo $id; ?>"
                   <?php echo in_array($id, $level->value) ? 'checked' : ''; ?>>
            <span><?php echo $option; ?></span>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</aside>
