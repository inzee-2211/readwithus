<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section padding-bottom-0">
  <div class="container container--fixed">
    <div class="intro-head">
      <h2 class="small-title"><?php echo strtoupper(Label::getLabel('LBL_FAQ')); ?></h2>
    </div>
  </div>
</section>

<section class="section section--faq rwu-faq-v3">
  <div class="rwu-faq-v3__bg"></div>
  <div class="container container--narrow">
    <div class="faq-cover">
      <?php if (empty($faqs) || !is_array($faqs)) { ?>
        <h2 class="text--center"><?php echo Label::getLabel('LBL_NO_FAQ_YET'); ?></h2>
      <?php } ?>

      <?php
        // Safely determine the first category id (without temporary expressions)
        $firstCatId = null;
        if (!empty($faqs) && is_array($faqs)) {
            foreach ($faqs as $k => $v) { $firstCatId = $k; break; }
        }
      ?>

      <?php if (!empty($faqs) && is_array($faqs)) { ?>
        <?php foreach ($faqs as $catId => $faqDetails) { ?>
          <?php
            // Split into left/right columns (alternating)
            $leftFaqs = [];
            $rightFaqs = [];
            $i = 0;
            if (is_array($faqDetails)) {
              foreach ($faqDetails as $q) {
                if ($i % 2 === 0) { $leftFaqs[] = $q; } else { $rightFaqs[] = $q; }
                $i++;
              }
            }
          ?>
          <div id="<?php echo 'section_' . (int)$catId; ?>"
               class="rwu-faq-v3__grid"
               <?php echo ($firstCatId !== null && $firstCatId !== $catId) ? 'style="display:none;"' : ''; ?>>

            <!-- LEFT column (blue tint) -->
            <div class="rwu-faq-v3__col" data-side="left">
              <?php foreach ($leftFaqs as $ques) { ?>
                <div class="rwu-acc faq-group-js" data-theme="left">
                  <button class="rwu-acc__head faq__trigger-js" aria-expanded="false" type="button">
                    <span class="rwu-acc__bullet" aria-hidden="true"></span>
                    <span class="rwu-acc__title"><?php echo $ques['faq_title']; ?></span>
                    <span class="rwu-acc__icon" aria-hidden="true">
                      <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M7 10l5 5 5-5z"/></svg>
                    </span>
                  </button>
                  <div class="rwu-acc__body faq__target-js" hidden>
                    <iframe srcdoc="<?php echo $ques['faq_description']; ?>" class="rwu-acc__iframe" loading="lazy"></iframe>
                  </div>
                </div>
              <?php } ?>
            </div>

            <!-- RIGHT column (purple tint) -->
            <div class="rwu-faq-v3__col" data-side="right">
              <?php foreach ($rightFaqs as $ques) { ?>
                <div class="rwu-acc faq-group-js" data-theme="right">
                  <button class="rwu-acc__head faq__trigger-js" aria-expanded="false" type="button">
                    <span class="rwu-acc__bullet" aria-hidden="true"></span>
                    <span class="rwu-acc__title"><?php echo $ques['faq_title']; ?></span>
                    <span class="rwu-acc__icon" aria-hidden="true">
                      <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M7 10l5 5 5-5z"/></svg>
                    </span>
                  </button>
                  <div class="rwu-acc__body faq__target-js" hidden>
                    <iframe srcdoc="<?php echo $ques['faq_description']; ?>" class="rwu-acc__iframe" loading="lazy"></iframe>
                  </div>
                </div>
              <?php } ?>
            </div>

          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
</section>

<?php
// Contact section kept as-is; visual spacing handled in CSS below
$this->includeTemplate('_partial/contact-us-section.php', ['siteLangId' => $siteLangId]);
?>

<style>
/* =========================
   RWU FAQ — Modern “Work of Art” (scoped)
   ========================= */
.rwu-faq-v3{
  --ink:#0A033C;
  --muted:#5F6C76;
  --white:#fff;
  --accent:#FF782D;
  --left-bg:#F0F7FF;   /* blue tint */
  --right-bg:#F7F5FF;  /* purple tint */
  --card:#FFFFFFE6;
  --ring:#E7ECF3;
  --radius:14px;
  --pad-x:26px; --pad-y:18px;
  --gap:30px;
  position: relative;
  overflow: hidden;
  font-family: Inter, Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial;
}
.rwu-faq-v3__bg{
  position:absolute; inset:0; z-index:0;
  background:white;
    /* radial-gradient(800px 400px at -10% 10%, #E6F3FF 0%, transparent 60%),
    radial-gradient(700px 380px at 110% 20%, #EFE6FF 0%, transparent 60%),
    linear-gradient(180deg, #FAFCFF 0%, #FFFFFF 100%); */
  /* pointer-events:none; */
}
.rwu-faq-v3 .container{ position:relative; z-index:1; }
.rwu-faq-v3__grid{
  display:grid; grid-template-columns:repeat(2,minmax(0,1fr));
  gap:var(--gap);
}
.rwu-faq-v3__col{ display:flex; flex-direction:column; gap:var(--gap); }
.rwu-faq-v3__col[data-side="left"] .rwu-acc{ background:var(--left-bg); }
.rwu-faq-v3__col[data-side="right"] .rwu-acc{ background:var(--right-bg); }

/* Card */
.rwu-acc{
  border-radius:var(--radius);
  border:1px solid var(--ring);
  box-shadow:0 10px 26px rgba(10,3,60,.06);
  transition: box-shadow .25s ease, transform .2s ease, background .2s ease;
}
.rwu-acc:hover{ transform:translateY(-2px); box-shadow:0 16px 32px rgba(10,3,60,.09); }

/* Head (button) */
.rwu-acc__head{
  all:unset; display:flex; align-items:center; gap:14px;
  padding:var(--pad-y) var(--pad-x);
  width:100%; cursor:pointer; border-radius:var(--radius);
}
.rwu-acc__title{
  flex:1 1 auto; font-weight:600; font-size:16px; line-height:1.25;
  color:#000; text-transform:capitalize;
}
.rwu-acc__bullet{
  width:10px; height:10px; border-radius:999px; flex:0 0 10px;
  background:linear-gradient(180deg,#9CCBFF 0%,#2DADFF 100%);
}
.rwu-acc[data-theme="right"] .rwu-acc__bullet{
  background:linear-gradient(180deg,#D7C7FF 0%,#8B5CF6 100%);
}
.rwu-acc__icon{
  color:#9D9D9D; display:inline-flex; transition: transform .2s ease, color .2s ease;
}
.rwu-acc.is-active .rwu-acc__icon{ transform:rotate(180deg); color:var(--accent); }
.rwu-acc.is-active .rwu-acc__title{ color:var(--accent); }

/* Body */
.rwu-acc__body{
  padding:0 var(--pad-x) var(--pad-y); color:#555; background:var(--card);
  border-radius:0 0 var(--radius) var(--radius);
  border-top:1px solid var(--ring);
}
.rwu-acc__iframe{ width:100%; border:0; height:35px; }

/* Active ring */
.rwu-acc.is-active{
  background:#fff;
  box-shadow:
    0 14px 38px rgba(10,3,60,.12),
    inset 0 0 0 2px rgba(255,120,45,.12);
  border-color:rgba(255,120,45,.35);
}

/* Contact section spacing */
.section + .section.section--faq + .section{ margin-top: 12px; }

/* Responsive */
@media (max-width: 991px){
  .rwu-faq-v3__grid{ grid-template-columns:1fr; }
}
@media (prefers-reduced-motion: reduce){
  .rwu-acc, .rwu-acc__icon{ transition:none; }
}
</style>

<script>
(function(){
  // Accordion toggle
  $('.faq__trigger-js').on('click', function(e){
    e.preventDefault();
    var $card = $(this).closest('.faq-group-js');
    var $body = $card.find('.faq__target-js');

    if ($card.hasClass('is-active')) {
      $card.removeClass('is-active');
      $body.attr('hidden', true);
      $(this).attr('aria-expanded','false');
    } else {
      var $grid = $(this).closest('.rwu-faq-v3__grid');
      $grid.find('.faq-group-js.is-active')
           .removeClass('is-active')
           .find('.faq__target-js').attr('hidden', true)
           .end().find('.faq__trigger-js').attr('aria-expanded','false');

      $card.addClass('is-active');
      $body.attr('hidden', false);
      $(this).attr('aria-expanded','true');

      // Auto-size iframe
      var $iframe = $body.find('iframe');
      setTimeout(function(){
        try{
          var h = $iframe[0].contentWindow.document.body.scrollHeight || 0;
          $iframe.height(Math.max(60, h));
        }catch(e){}
      }, 50);
    }
  });

  // Iframe load sizing (safety)
  $('.rwu-acc__iframe').each(function(){
    var ifr = this;
    $(ifr).on('load', function(){
      try{
        var h = ifr.contentWindow.document.body.scrollHeight || 0;
        if (h) $(ifr).height(Math.max(60, h));
      }catch(e){}
    });
  });
})();
</script>
