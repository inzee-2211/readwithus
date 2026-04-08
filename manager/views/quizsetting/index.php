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


</div>

<!-- Modal Container for Forms -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true"></div>

<script>
// Open form in modal
function openForm(type, id) {
  const url = '<?= MyUtility::makeUrl('Quizsetting', 'form'); ?>' + `?type=${type}&id=${id}`;
  $.get(url, function (html) {
    if ($.facebox) {
      $.facebox(html);
    } else {
      const $m = $('#formModal');
      $m.html(html).modal ? $m.modal('show') : $m.show();
    }
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
<style>
  /* ============ Quiz Settings – visual tidy ============ */
.page .card {
  border: 1px solid #e6e6e6;
  border-radius: 12px;
  width: 90%;
  margin-left: 5px;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
  margin: 18px 0;
  overflow: hidden;
  background: #fff;
}

.page .card__head {
  padding: 14px 18px;
  border-bottom: 1px solid #f0f0f0;
}
.page .card__head h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
}

/* .page .btn.btn-primary {
  padding: 8px 14px;
  border-radius: 10px;
  border: none;
} */

.page .card__body { padding: 0; }

.page .table {
  width: 99%;
  border-collapse: separate;
  border-spacing: 0;
  table-layout: fixed;
}
.page .table thead th {
  background: #fafafa;
  font-weight: 600;
  font-size: 13px;
  color: #444;
  padding: 12px 14px;
  border-bottom: 1px solid #eee;
}
/* .page .table td {
  padding: 12px 14px;
  border-bottom: 1px solid #f4f4f4;
  vertical-align: middle;
  font-size: 14px;
} */

/* Make all sections feel aligned: narrow ID, right-sized ACTIONS */
.page .table thead th:first-child,
.page .table td:first-child { width: 72px; text-align: left; }

.page .table thead th:last-child,
.page .table td:last-child { width: 180px; text-align: right; white-space: nowrap; }

/* Links in ACTIONS */
.page .table td a {
  color: #e65100; /* matches your existing orange-ish */
  text-decoration: none;
  margin-left: 8px;
}
.page .table td a:first-child { margin-left: 0; }
.page .table td a:hover { text-decoration: underline; }
/* ===== Column width system (adjust here) ===== */

/* 3-column tables: [ID] [MAIN] [ACTIONS] */
.page .card .table thead tr:has(th:nth-child(3):last-child) th:nth-child(1) { width: 80px; }    /* <-- tweak ID width */
.page .card .table thead tr:has(th:nth-child(3):last-child) th:nth-child(3) { width: 180px; }   /* <-- tweak ACTIONS width */
/* MAIN column (2nd) will auto-fill remaining space */

/* 4-column tables: [ID] [COL-2] [COL-3] [ACTIONS] */
.page .card .table thead tr:has(th:nth-child(4):last-child) th:nth-child(1) { width: 80px; }    /* <-- tweak ID width */
.page .card .table thead tr:has(th:nth-child(4):last-child) th:nth-child(2) { width: 22%; }     /* <-- tweak COL-2 width */
.page .card .table thead tr:has(th:nth-child(4):last-child) th:nth-child(3) { width: auto; }    /* <-- tweak COL-3 width (flex) */
.page .card .table thead tr:has(th:nth-child(4):last-child) th:nth-child(4) { width: 180px; }   /* <-- tweak ACTIONS width */

/* Make body columns follow header widths */
.page .card .table tbody tr:has(td:nth-child(3):last-child) td:nth-child(1) { width: 80px; }    /* for 3-col rows */
.page .card .table tbody tr:has(td:nth-child(3):last-child) td:last-child   { width: 180px; text-align: right; white-space: nowrap; }

.page .card .table tbody tr:has(td:nth-child(4):last-child) td:nth-child(1) { width: 80px; }    /* for 4-col rows */
.page .card .table tbody tr:has(td:nth-child(4):last-child) td:nth-child(2) { width: 22%; }
.page .card .table tbody tr:has(td:nth-child(4):last-child) td:last-child   { width: 180px; text-align: right; white-space: nowrap; }


/* Facebox / modal tidy (works whether you use facebox or bootstrap markup) */
#facebox .modal-header .close { display: none !important; }
#facebox .content,
.facebox .content,
.modal .modal-content {
  border-radius: 14px !important;
  border: 1px solid #eaeaea;
  box-shadow: 0 10px 30px rgba(0,0,0,.12);
}

#facebox .content { width: 640px; max-width: calc(100vw - 40px); }

.modal-header,
#facebox .modal-header {
  padding: 14px 18px;
  border-bottom: 1px solid #f0f0f0;
}
.modal-title { font-size: 16px; font-weight: 700; }

.form__body { padding: 16px 18px; }
.form__footer { padding: 12px 18px 18px; border-top: 1px solid #f7f7f7; }

.form__body .field-set,
.form__body .field-set .field {
  width: 100%;
}

.form__body input[type="text"],
.form__body input[type="number"],
.form__body select,
.form__body .textbox,
.form__body .selectbox {
  width: 100% !important;
  min-height: 40px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 8px 12px;
  font-size: 14px;
  background: #fff;
}

.form__footer .btn,
.form__footer input[type="submit"] {
  padding: 9px 16px;
  border-radius: 10px;
  border: none;
}

/* Hover states and subtle polish */
.page .table tbody tr:hover { background: #fcfcfc; }
.page .btn.btn-primary:hover { filter: brightness(0.96); }

@media (max-width: 768px) {
  .page .table thead { display: none; }
  .page .table tr { display: block; padding: 10px 12px; }
  .page .table td { display: flex; justify-content: space-between; border: 0; padding: 8px 0; }
  .page .table td:last-child { width: 100%; text-align: left; }
}

</style>