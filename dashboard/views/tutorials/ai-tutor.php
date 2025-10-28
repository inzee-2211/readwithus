<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="aiTutor">
  <!-- Header -->
  <div class="aiTutor__header">
    <div>
      <h5 class="aiTutor__title m-0"><?php echo Label::getLabel('LBL_AI_TUTOR'); ?></h5>
      <small class="aiTutor__subtitle text-muted">
        <?php echo Label::getLabel('LBL_ASK_ANYTHING_ABOUT_THIS_LECTURE_,_ONLY_100_CHATS_ARE_ALLOWED_YOU_WILL_BE_CHARGED_FOR_MORE'); ?>
      </small>
    </div>
    <div class="aiTutor__actions">
      <button type="button" class="btn btn--sm btn--light" id="aiClearBtn">
        <i class="fa fa-eraser"></i> <?php echo Label::getLabel('LBL_CLEAR_CHAT'); ?>
      </button>
    </div>
  </div>

  <!-- Suggestion Pills -->
  <div class="aiTutor__suggestions">
    <span class="ai-pill ai-suggestion"><i class="fa fa-lightbulb"></i> Summarize this lecture</span>
    <span class="ai-pill ai-suggestion"><i class="fa fa-child"></i> Explain like I’m 12</span>
    <span class="ai-pill ai-suggestion"><i class="fa fa-pen"></i> Give 3 practice questions</span>
    <span class="ai-pill ai-suggestion"><i class="fa fa-calculator"></i> Show key formulas</span>
  </div>

  <!-- Messages -->
  <div class="aiTutor__body">
    <div class="aiTutor__messages" id="aiMessages">
      <div class="ai-msg ai-msg--bot ai-welcome">
        <div class="ai-msg__avatar">
          <i class="fa fa-robot"></i>
        </div>
        <div class="ai-msg__bubble">
          <?php echo Label::getLabel('LBL_HI_I_AM_YOUR_AI_TUTOR_ASK_ME_ANYTHING_ABOUT_THIS_LECTURE_OR_SECTION'); ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Composer -->
  <div class="aiTutor__composer">
  <textarea id="aiInput" rows="2" placeholder="<?php echo Label::getLabel('LBL_TYPE_YOUR_QUESTION'); ?>"></textarea>
  
  <button type="button" class="aiSendBtn" id="aiSendBtn" title="Send message">
    <i class="fa fa-paper-plane"></i>
  </button>
</div>


<style>
/* ========== CORE LAYOUT ========== */
.aiTutor {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 180px);
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  background: #ffffff;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0,0,0,0.05);
  font-family: 'Inter', 'Segoe UI', sans-serif;
}
/* === Send Button Styling === */
.aiSendBtn {
  width: 50px;
  height: 50px;
  border: none;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff5e3a 0%, #ff2a68 100%);
  color: #fff;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 10px rgba(255, 90, 90, 0.3);
}

.aiSendBtn:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 6px 14px rgba(255, 90, 90, 0.4);
}

.aiSendBtn:active {
  transform: scale(0.95);
  box-shadow: 0 2px 6px rgba(255, 90, 90, 0.25);
}

/* Optional: floating “pulse” animation to make it alive */
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(255, 90, 90, 0.4); }
  70% { box-shadow: 0 0 0 8px rgba(255, 90, 90, 0); }
  100% { box-shadow: 0 0 0 0 rgba(255, 90, 90, 0); }
}
.aiSendBtn.ready {
  animation: pulse 2s infinite;
}

/* Adjust layout for balance */
.aiTutor__composer {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-top: 1px solid #f1f1f1;
  background: #fafafa;
}

.aiTutor__composer textarea {
  flex: 1;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 14px;
  font-size: 14px;
  line-height: 1.5;
  resize: none;
  transition: border-color 0.2s;
}

.aiTutor__composer textarea:focus {
  outline: none;
  border-color: #ff5e3a;
  box-shadow: 0 0 0 2px rgba(255, 90, 90, 0.15);
}

/* Header */
.aiTutor__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 14px 18px;
  border-bottom: 1px solid #f1f1f1;
  background: #fafafa;
}
.aiTutor__title {
  font-weight: 700;
  font-size: 16px;
  color: #111827;
}
.aiTutor__subtitle {
  font-size: 13px;
  color: #6b7280;
}

/* Suggestion Pills */
.aiTutor__suggestions {
  padding: 10px 14px;
  border-bottom: 1px dashed #e5e7eb;
  background: #fcfcfc;
  overflow-x: auto;
  white-space: nowrap;
}
.ai-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  font-size: 13px;
  border-radius: 50px;
  background: #fff;
  border: 1px solid #e5e7eb;
  color: #374151;
  margin-right: 8px;
  transition: all 0.2s ease;
  cursor: pointer;
}
.ai-pill:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}

/* Chat Body */
.aiTutor__body {
  flex: 1;
  overflow-y: auto;
  background: #fff;
}
.aiTutor__messages {
  padding: 20px;
}

/* Messages */
.ai-msg {
  display: flex;
  margin-bottom: 16px;
  max-width: 90%;
  animation: fadeIn 0.3s ease;
}
.ai-msg--user { flex-direction: row-reverse; margin-left: auto; text-align: right; }
.ai-msg__avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
  margin-right: 10px;
  background: #111827;
  color: #fff;
}
.ai-msg--user .ai-msg__avatar {
  background: #10b981;  /* emerald green */
  margin-right: 0; margin-left: 10px;
}
.ai-msg__bubble {
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  line-height: 1.5;
  color: #111827;
  white-space: pre-wrap;
  word-wrap: break-word;
}
.ai-msg--user .ai-msg__bubble {
  background: #dcfce7;
  border-color: #bbf7d0;
  color: #065f46;
}

/* Composer */
.aiTutor__composer {
  display: flex;
  gap: 8px;
  padding: 14px;
  border-top: 1px solid #f1f1f1;
  background: #fafafa;
}
.aiTutor__composer textarea {
  flex: 1;
  resize: none;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 12px;
  font-size: 14px;
  line-height: 1.5;
  background: #fff;
  transition: border-color 0.2s;
}
.aiTutor__composer textarea:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.15);
}
.aiTutor__composer .btn {
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  width: 44px; height: 44px;
  font-size: 18px;
  transition: all 0.2s ease;
}
.aiTutor__composer .btn:hover {
  transform: translateY(-1px);
}

/* Animation */
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(6px);}
  to {opacity: 1; transform: translateY(0);}
}

/* Scrollbar (light) */
.aiTutor__body::-webkit-scrollbar {
  width: 6px;
}
.aiTutor__body::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 3px;
}
</style>
