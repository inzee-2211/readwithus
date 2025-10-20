<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
  <div class="page__head">
    <h1><?= Label::getLabel('LBL_QUIZ_SETTINGS'); ?></h1>
  </div>

  <!-- LEVELS -->
  <section class="card">
    <div class="card__head d-flex justify-content-between align-items-center">
      <h3><?= Label::getLabel('LBL_LEVELS'); ?></h3>
      <a class="btn btn-primary" href="javascript:void(0)" 
         onclick="openForm('level', 0)">
        <?= Label::getLabel('LBL_ADD_LEVEL'); ?>
      </a>
    </div>
    <div class="card__body">
      <table class="table">
        <thead>
          <tr><th>ID</th><th><?= Label::getLabel('LBL_LEVEL'); ?></th><th><?= Label::getLabel('LBL_ACTIONS'); ?></th></tr>
        </thead>
        <tbody>
          <?php foreach ($levels as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td>
              <td><?= $r['level_name']; ?></td>
              <td>
                <a href="javascript:void(0)" 
                   onclick="openForm('level', <?= $r['id']; ?>)">
                  <?= Label::getLabel('LBL_EDIT'); ?>
                </a>
                |
                <a class="js-delete"
                   data-id="<?= $r['id']; ?>"
                   data-type="level"
                   href="javascript:void(0);">
                  <?= Label::getLabel('LBL_DELETE'); ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- SUBJECTS -->
  <section class="card">
    <div class="card__head d-flex justify-content-between align-items-center">
      <h3><?= Label::getLabel('LBL_SUBJECTS'); ?></h3>
      <a class="btn btn-primary" href="javascript:void(0)" 
         onclick="openForm('subject', 0)">
        <?= Label::getLabel('LBL_ADD_SUBJECT'); ?>
      </a>
    </div>
    <div class="card__body">
      <table class="table">
        <thead>
          <tr><th>ID</th><th><?= Label::getLabel('LBL_LEVEL'); ?></th><th><?= Label::getLabel('LBL_SUBJECT'); ?></th><th><?= Label::getLabel('LBL_ACTIONS'); ?></th></tr>
        </thead>
        <tbody>
          <?php foreach ($subjects as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td>
              <td><?= $r['level_name']; ?></td>
              <td><?= $r['subject']; ?></td>
              <td>
                <a href="javascript:void(0)" 
                   onclick="openForm('subject', <?= $r['id']; ?>)">
                  <?= Label::getLabel('LBL_EDIT'); ?>
                </a>
                |
                <a class="js-delete"
                   data-id="<?= $r['id']; ?>"
                   data-type="subject"
                   href="javascript:void(0);">
                  <?= Label::getLabel('LBL_DELETE'); ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- TYPES -->
  <section class="card">
    <div class="card__head d-flex justify-content-between align-items-center">
      <h3><?= Label::getLabel('LBL_TYPES'); ?></h3>
      <a class="btn btn-primary" href="javascript:void(0)" 
         onclick="openForm('type', 0)">
        <?= Label::getLabel('LBL_ADD_TYPE'); ?>
      </a>
    </div>
    <div class="card__body">
      <table class="table">
        <thead><tr><th>ID</th><th><?= Label::getLabel('LBL_TYPE'); ?></th><th><?= Label::getLabel('LBL_ACTIONS'); ?></th></tr></thead>
        <tbody>
          <?php foreach ($types as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td>
              <td><?= $r['name']; ?></td>
              <td>
                <a href="javascript:void(0)" 
                   onclick="openForm('type', <?= $r['id']; ?>)">
                  <?= Label::getLabel('LBL_EDIT'); ?>
                </a>
                |
                <a class="js-delete"
                   data-id="<?= $r['id']; ?>"
                   data-type="type"
                   href="javascript:void(0);">
                  <?= Label::getLabel('LBL_DELETE'); ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- EXAM BOARDS -->
  <section class="card">
    <div class="card__head d-flex justify-content-between align-items-center">
      <h3><?= Label::getLabel('LBL_EXAM_BOARDS'); ?></h3>
      <a class="btn btn-primary" href="javascript:void(0)" 
         onclick="openForm('examboard', 0)">
        <?= Label::getLabel('LBL_ADD_EXAM_BOARD'); ?>
      </a>
    </div>
    <div class="card__body">
      <table class="table">
        <thead><tr><th>ID</th><th><?= Label::getLabel('LBL_EXAM_BOARD'); ?></th><th><?= Label::getLabel('LBL_ACTIONS'); ?></th></tr></thead>
        <tbody>
          <?php foreach ($examBoards as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td>
              <td><?= $r['name']; ?></td>
              <td>
                <a href="javascript:void(0)" 
                   onclick="openForm('examboard', <?= $r['id']; ?>)">
                  <?= Label::getLabel('LBL_EDIT'); ?>
                </a>
                |
                <a class="js-delete"
                   data-id="<?= $r['id']; ?>"
                   data-type="examboard"
                   href="javascript:void(0);">
                  <?= Label::getLabel('LBL_DELETE'); ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- TIERS -->
  <section class="card">
    <div class="card__head d-flex justify-content-between align-items-center">
      <h3><?= Label::getLabel('LBL_TIERS'); ?></h3>
      <a class="btn btn-primary" href="javascript:void(0)" 
         onclick="openForm('tier', 0)">
        <?= Label::getLabel('LBL_ADD_TIER'); ?>
      </a>
    </div>
    <div class="card__body">
      <table class="table">
        <thead><tr><th>ID</th><th><?= Label::getLabel('LBL_EXAM_BOARD'); ?></th><th><?= Label::getLabel('LBL_TIER'); ?></th><th><?= Label::getLabel('LBL_ACTIONS'); ?></th></tr></thead>
        <tbody>
          <?php foreach ($tiers as $r): ?>
            <tr>
              <td><?= $r['id']; ?></td>
              <td><?= $r['examboard_name']; ?></td>
              <td><?= $r['name']; ?></td>
              <td>
                <a href="javascript:void(0)" 
                   onclick="openForm('tier', <?= $r['id']; ?>)">
                  <?= Label::getLabel('LBL_EDIT'); ?>
                </a>
                |
                <a class="js-delete"
                   data-id="<?= $r['id']; ?>"
                   data-type="tier"
                   href="javascript:void(0);">
                  <?= Label::getLabel('LBL_DELETE'); ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<!-- Modal Container for Forms -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true"></div>

<script>
// Open form in modal
function openForm(type, id) {
  const url = '<?= MyUtility::makeUrl('Quizsetting', 'form'); ?>' + `?type=${type}&id=${id}`;
  $.get(url, function (html) {
    $.facebox(html); // uses the existing facebox styling/JS
  });
}


// Universal delete handler
document.querySelectorAll('.js-delete').forEach(a => {
    a.addEventListener('click', function() {
        if (!confirm('<?= Label::getLabel('LBL_CONFIRM_DELETE'); ?>')) return;

        const formData = new FormData();
        formData.append('id', this.dataset.id);
        formData.append('entity_type', this.dataset.type);

        fetch('<?= MyUtility::makeUrl('Quizsetting', 'delete'); ?>', {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.status == 1) {
                location.reload();
            } else {
                alert(result.msg || 'Error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error occurred');
        });
    });
});

// Form submission handler
$(document).on('submit', 'form[id^="frm"]', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= MyUtility::makeUrl('Quizsetting', 'save'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status == 1) {
            location.reload();
        } else {
            alert(result.msg || 'Error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred');
    });
});
</script>