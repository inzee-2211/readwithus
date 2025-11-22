<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); 
// Ensure $selected is always an array
$selected = isset($selected) && is_array($selected) ? $selected : [];
?>
<style>
  /* Enhanced styling with better visual hierarchy */
  .subject-selection {
    max-width: 1000px;
    margin: 0 auto;
  }
  
  .subj-header {
    background: linear-gradient(135deg, #2DADFF 0%, skyblue 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .subj-header h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
    font-weight: 700;
  }
  
  .subj-instructions {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
  }
  
  .subj-instructions ul {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
  }
  
  .subj-instructions li {
    margin-bottom: 0.5rem;
    color: #4a5568;
  }
  
  .subj-toolbar {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    margin: 1.5rem 0;
    padding: 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }
  
  .subj-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 50px;
    background: #fff;
    font-weight: 600;
    color: #2d3748;
  }
  
  .subj-pill .count {
    background: #4299e1;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
  }
  
  .subj-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  
  .subj-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .subj-card:hover {
    border-color: #4299e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 153, 225, 0.15);
  }
  
  .subj-card.selected {
    border-color: #4299e1;
    background: #ebf8ff;
  }
  
  .subj-card input {
    width: 20px;
    height: 20px;
    cursor: pointer;
  }
  
  .subj-card span {
    font-weight: 500;
    color: #2d3748;
    flex: 1;
  }
  
  .subj-search {
    flex: 1;
    min-width: 280px;
  }
  
  .subj-search input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s ease;
  }
  
  .subj-search input:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
  }
  
  .subj-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
  }
  
  .btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
  }
  
  .btn--primary {
    background: #4299e1;
    color: white;
  }
  
  .btn--primary:hover {
    background: #3182ce;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
  }
  
  .btn--secondary {
    background: #fff;
    color: #4a5568;
    border: 2px solid #e2e8f0;
  }
  
  .btn--secondary:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
  }
  
  .limit-message {
    background: #fffaf0;
    border: 1px solid #fed7d7;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    color: #c53030;
    font-weight: 500;
    display: none;
  }
  
  .no-results {
    text-align: center;
    padding: 3rem;
    color: #718096;
    grid-column: 1 / -1;
    display: none;
  }
  
  .is-disabled {
    opacity: 0.6;
    pointer-events: none;
  }
  
  .subj-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .subj-card.disabled:hover {
    border-color: #e2e8f0;
    transform: none;
    box-shadow: none;
  }
  
  /* Confirmation Modal */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  
  .modal-content {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
  }
  
  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
  }
  
  .btn--danger {
    background: #e53e3e;
    color: white;
  }
  
  .btn--danger:hover {
    background: #c53030;
  }
  
  @media (max-width: 768px) {
    .subj-toolbar {
      flex-direction: column;
      align-items: stretch;
    }
    
    .subj-search {
      min-width: auto;
    }
    
    .subj-actions {
      justify-content: center;
    }
    
    .subj-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<section class="section section--page">
  <div class="container container--fixed">
    <div class="subject-selection">
      <!-- Header Section -->
      <div class="subj-header">
        <h2>Choose Your Learning Path</h2>
        <p>Select up to <?= (int)$limit ?> subjects to customize your educational journey</p>
      </div>
      
      <!-- Instructions Section -->
      <div class="subj-instructions">
        <h3 style="margin: 0 0 0.75rem 0; color: #2d3748;">📚 Important Information</h3>
        <ul>
          <li>You can select <strong>up to <?= (int)$limit ?> subjects</strong> with your current plan</li>
          <li>Your selected subjects determine which courses you can access</li>
          <li>You cannot modify your subject choices later </li>
          <li>Choose carefully - these selections will guide your learning experience</li>
        </ul>
      </div>

      <!-- Toolbar -->
      <div class="subj-toolbar">
        <div class="subj-search">
          <input id="subjSearch" type="text" placeholder="Search subjects...">
        </div>

        <div class="subj-pill">
          <span class="count" id="selCount"><?= count($selected) ?></span>
          <span>subjects selected</span>
          <span class="subj-muted">/ <?= (int)$limit ?> maximum</span>
        </div>

        <div class="subj-actions">
          <button class="btn btn--secondary" type="button" id="btnClear">Clear All</button>
          <button class="btn btn--primary" type="button" id="btnSave">Continue to Checkout</button>
        </div>
      </div>

      <!-- Subjects Grid -->
      <form id="subjForm" onsubmit="return false;">
        <div class="subj-grid" id="subjGrid">
          <?php foreach ($subjects as $s): ?>
            <?php
              $id   = (int)$s['subject_id'];
              $name = (string)$s['subject_name'];
              $checked = in_array((string)$id, $selected, true);
            ?>
            <label class="subj-card <?= $checked ? 'selected' : '' ?>" 
                   data-name="<?= htmlspecialchars(mb_strtolower($name), ENT_QUOTES, 'UTF-8') ?>">
              <input type="checkbox" name="subject_ids[]" value="<?= $id ?>" <?= $checked ? 'checked' : '' ?>>
              <span><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
            </label>
          <?php endforeach; ?>
          <div class="no-results" id="noResults">
            <p>No subjects found matching your search.</p>
            <p>Try different keywords or clear your search.</p>
          </div>
        </div>
      </form>

      <!-- Limit Message -->
      <div class="limit-message" id="limitMsg">
        <strong>Maximum selection reached</strong>
        <p style="margin: 0.5rem 0 0 0;">You've selected the maximum of <?= (int)$limit ?> subjects. Unselect one to choose another.</p>
      </div>
    </div>
  </div>
</section>

<!-- Confirmation Modal -->
<div class="modal-overlay" id="confirmationModal">
  <div class="modal-content">
    <h3 style="margin: 0 0 1rem 0; color: #2d3748;">Ready to Continue?</h3>
    <p>You've selected <strong id="confirmCount">0</strong> subjects. You'll be able to modify these selections later from your dashboard.</p>
    <p><strong>Next:</strong> You'll be redirected to secure checkout to complete your subscription.</p>
    
    <div class="modal-actions">
      <button class="btn btn--secondary" type="button" id="btnCancel">Go Back</button>
      <button class="btn btn--primary" type="button" id="btnConfirm">Yes, Continue to Checkout</button>
    </div>
  </div>
</div>

<script>
(function(){
  const limit = <?= (int)$limit ?>;
  const grid = document.getElementById('subjGrid');
  const form = document.getElementById('subjForm');
  const count = document.getElementById('selCount');
  const searchInput = document.getElementById('subjSearch');
  const btnSave = document.getElementById('btnSave');
  const btnClear = document.getElementById('btnClear');
  const limitMsg = document.getElementById('limitMsg');
  const noResults = document.getElementById('noResults');
  const confirmationModal = document.getElementById('confirmationModal');
  const btnCancel = document.getElementById('btnCancel');
  const btnConfirm = document.getElementById('btnConfirm');
  const confirmCount = document.getElementById('confirmCount');

  function selectedCount() { 
    return form.querySelectorAll('input[type="checkbox"]:checked').length; 
  }

  function updateCounter() {
    const c = selectedCount();
    count.textContent = c;
    const full = c >= limit;
    
    // Update card states
    form.querySelectorAll('.subj-card').forEach(card => {
      const checkbox = card.querySelector('input[type="checkbox"]');
      if (!checkbox.checked) {
        card.classList.toggle('disabled', full);
        checkbox.disabled = full;
      }
    });
    
    // Show/hide limit message
    limitMsg.style.display = full ? 'block' : 'none';
  }

  function updateSearchResults() {
    const q = searchInput.value.trim().toLowerCase();
    let visibleCount = 0;
    
    grid.querySelectorAll('.subj-card').forEach(card => {
      const name = card.getAttribute('data-name') || '';
      const isVisible = name.includes(q);
      card.style.display = isVisible ? '' : 'none';
      if (isVisible) visibleCount++;
    });
    
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
  }

  function showConfirmation() {
    const selected = selectedCount();
    if (selected === 0) {
      if (typeof $.mbsmessage === 'function') {
        $.mbsmessage('Please select at least one subject to continue.', true, 'alert--danger');
      } else {
        alert('Please select at least one subject to continue.');
      }
      return;
    }
    
    confirmCount.textContent = selected;
    confirmationModal.style.display = 'flex';
  }

  function hideConfirmation() {
    confirmationModal.style.display = 'none';
  }

  function saveSubjects() {
    const url = (typeof fcom !== 'undefined')
      ? fcom.makeUrl('Subscription', 'processSubscription')
      : '<?= MyUtility::makeUrl('Subscription','processSubscription'); ?>';

    btnConfirm.classList.add('is-disabled');
    btnConfirm.textContent = 'Processing...';

    $.ajax({
      url: url,
      type: 'POST',
      data: $(form).serialize(),
      dataType: 'json',
      success: function(json) {
        btnConfirm.classList.remove('is-disabled');
        btnConfirm.textContent = 'Yes, Continue to Checkout';
        
        if (json && json.status == 1) {
          if (typeof $.mbsmessage === 'function') {
            $.mbsmessage(json.msg || 'Subjects selected successfully!', true, 'alert--success');
          }
          hideConfirmation();
          if (json.redirectUrl) {
            setTimeout(() => {
              location.href = json.redirectUrl;
            }, 1000);
          }
        } else {
          const msg = (json && json.msg) ? json.msg : 'Failed to save subjects';
          if (typeof $.mbsmessage === 'function') {
            $.mbsmessage(msg, true, 'alert--danger');
          } else {
            alert(msg);
          }
        }
      },
      error: function(xhr) {
        btnConfirm.classList.remove('is-disabled');
        btnConfirm.textContent = 'Yes, Continue to Checkout';
        const msg = xhr.responseText || 'Request failed. Please try again.';
        if (typeof $.mbsmessage === 'function') {
          $.mbsmessage(msg, true, 'alert--danger');
        } else {
          alert(msg);
        }
        console.error('processSubscription error:', xhr);
      }
    });
  }

  // Initialize
  updateCounter();

  // Event Listeners
  grid.addEventListener('change', function(e) {
    if (e.target && e.target.type === 'checkbox') {
      const card = e.target.closest('.subj-card');
      if (e.target.checked) {
        if (selectedCount() > limit) {
          e.target.checked = false;
          limitMsg.style.display = 'block';
          return;
        }
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
      updateCounter();
    }
  });

  // Click on card toggles checkbox
  grid.addEventListener('click', function(e) {
    if (e.target.closest('.subj-card') && !e.target.matches('input')) {
      const card = e.target.closest('.subj-card');
      const checkbox = card.querySelector('input[type="checkbox"]');
      if (!checkbox.disabled) {
        checkbox.checked = !checkbox.checked;
        checkbox.dispatchEvent(new Event('change'));
      }
    }
  });

  searchInput.addEventListener('input', updateSearchResults);

  btnClear.addEventListener('click', function() {
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
      cb.checked = false;
      cb.dispatchEvent(new Event('change'));
    });
    updateCounter();
  });

  btnSave.addEventListener('click', showConfirmation);
  btnCancel.addEventListener('click', hideConfirmation);
  btnConfirm.addEventListener('click', saveSubjects);

  // Close modal when clicking outside
  confirmationModal.addEventListener('click', function(e) {
    if (e.target === confirmationModal) {
      hideConfirmation();
    }
  });

})();
</script>