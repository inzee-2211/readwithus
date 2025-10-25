<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="aiTutor">
  <div class="aiTutor__header">
    <div>
      <h5 class="m-0"><?php echo Label::getLabel('LBL_AI_TUTOR'); ?></h5>
      <small class="text-muted"><?php echo Label::getLabel('LBL_EXPERIMENTAL_UI__NO_API_CONNECTED'); ?></small>
    </div>
    <!-- <div class="aiTutor__actions">
      <button type="button" class="btn btn--sm btn--secondary" id="aiClearBtn">
        <?php echo Label::getLabel('LBL_CLEAR'); ?>
      </button>
      <button type="button" class="btn btn--sm btn--primary" id="aiConnectBtn">
        <?php echo Label::getLabel('LBL_CONNECT_API_LATER'); ?>
      </button>
    </div> -->
  </div>

  <div class="aiTutor__suggestions">
    <span class="ai-pill ai-suggestion">Summarize this lecture</span>
    <span class="ai-pill ai-suggestion">Explain like I’m 12</span>
    <span class="ai-pill ai-suggestion">Give 3 practice questions</span>
    <span class="ai-pill ai-suggestion">Show key formulas</span>
  </div>

  <div class="aiTutor__body">
    <div class="aiTutor__messages" id="aiMessages">
      <!-- Example welcome -->
      <div class="ai-msg ai-msg--bot">
        <div class="ai-msg__avatar">AI</div>
        <div class="ai-msg__bubble">
          <?php echo Label::getLabel('LBL_HI_I_AM_YOUR_AI_TUTOR_ASK_ME_ANYTHING_ABOUT_THIS_LECTURE_OR_SECTION'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="aiTutor__composer">
    <textarea id="aiInput" rows="2" placeholder="<?php echo Label::getLabel('LBL_TYPE_YOUR_QUESTION'); ?>"></textarea>
    <button type="button" class="btn btn--primary" id="aiSendBtn">
      <?php echo Label::getLabel('LBL_SEND'); ?>
    </button>
  </div>
</div>
<style>
    .aiTutor { display:flex; flex-direction:column; height: calc(100vh - 220px); border:1px solid #eee; border-radius:12px; overflow:hidden; }
.aiTutor__header { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid #eee; background:#fff; }
.aiTutor__actions .btn + .btn { margin-left:8px; }

.aiTutor__suggestions { padding:10px 12px; border-bottom:1px dashed #eee; background:#fafafa; overflow-x:auto; }
.ai-pill { display:inline-block; padding:6px 10px; border-radius:999px; border:1px solid #e2e2e2; margin-right:8px; white-space:nowrap; cursor:pointer; }

.aiTutor__body { flex:1; overflow:auto; background:#fff; }
.aiTutor__messages { padding:16px; }

.ai-msg { display:flex; margin-bottom:14px; max-width:900px; }
.ai-msg--user { flex-direction:row-reverse; margin-left:auto; }
.ai-msg__avatar { width:28px; height:28px; background:#222; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; margin-right:8px; }
.ai-msg--user .ai-msg__avatar { margin-right:0; margin-left:8px; background:#00796b; }
.ai-msg__bubble { border:1px solid #eaeaea; background:#fdfdfd; padding:10px 12px; border-radius:12px; line-height:1.45; }
.ai-msg--user .ai-msg__bubble { background:#e9f7f5; border-color:#d4f0ec; }

.aiTutor__composer { display:flex; gap:8px; padding:12px; border-top:1px solid #eee; background:#fff; }
.aiTutor__composer textarea { flex:1; resize:none; border:1px solid #ddd; border-radius:10px; padding:10px; }

</style>