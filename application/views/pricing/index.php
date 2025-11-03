<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
function asset_css($file){
  $abs = CONF_APPLICATION_PATH . 'public/' . ltrim($file,'/');
  return CONF_WEBROOT_URL . $file . '?v=' . (@filemtime($abs) ?: time());
}
$symbolLeft  = $siteCurrency['currency_symbol_left'] ?? '';
$symbolRight = $siteCurrency['currency_symbol_right'] ?? '';
?>
<link rel="stylesheet" href="<?= asset_css('css/home.pricing.css') ?>">

<style>
/* ===== Page extras for Pricing ===== */
.pricing-hero{
  background: linear-gradient(180deg,#F5FAFF 0%,#FFFFFF 100%);
  padding: 64px 16px 28px;
  text-align:center;
}
.pricing-hero .eyebrow{ display:inline-flex; gap:8px; align-items:center; padding:8px 14px; border-radius:999px; background:#C2E6FF; color:#1D9CFD; font-weight:600; margin-bottom:12px;}
.pricing-hero h1{ font:700 clamp(28px,3.6vw,44px)/1.2 Poppins,system-ui; color:#0A033C; margin:0 0 10px;}
.pricing-hero p{ max-width:820px; margin:0 auto; color:#5F6C76; line-height:1.7;}

.billing-toggle{ display:flex; gap:10px; justify-content:center; align-items:center; margin:24px 0 8px;}
.billing-toggle .switch{
  --h:34px; position:relative; width:80px; height:var(--h); background:#eaf5ff; border-radius:999px; cursor:pointer;
}
.billing-toggle .knob{
  position:absolute; top:3px; left:3px; width:28px; height:28px; border-radius:50%; background:#2DADFF; transition: left .2s ease;
}
.billing-toggle input{ display:none; }
.billing-toggle input:checked + .switch .knob{ left:49px; }
.billing-toggle .save{ color:#0a8bff; font-weight:600; background:#e6f4ff; padding:4px 8px; border-radius:6px; }

.rwu-plan.is-popular{ outline:2px solid rgba(45,173,255,.55); background:#fff; }
.rwu-plan .badge-pop{ position:absolute; top:12px; left:12px; background:#2DADFF; color:#fff; font:600 12px/1 Inter; padding:6px 8px; border-radius:8px; }

.section-wrap{ max-width:1200px; margin:0 auto; padding:48px 16px; }
.compare{ overflow:auto; }
.compare table{ width:100%; border-collapse:collapse; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.05); overflow:hidden;}
.compare th, .compare td{ padding:14px 16px; border-bottom:1px solid #eef3f7; text-align:left; font-size:14.5px;}
.compare thead th{ background:#f6fbff; font-weight:700; color:#0A033C; }
.compare tbody tr:hover{ background:#fcfdff; }
.compare .yes{ color:#10893e; font-weight:600; }
.compare .no{ color:#b00020; font-weight:600; }

.faq{ max-width:900px; margin:0 auto; }
.faq .q{ background:#f6fbff; border:1px solid #e6f1ff; padding:14px 16px; border-radius:10px; margin:10px 0; cursor:pointer; font-weight:600; color:#0A033C;}
.faq .a{ display:none; padding:12px 2px 8px 6px; color:#5F6C76; }
.cta-final{ text-align:center; padding:48px 16px 64px; }
.cta-final .btn{ display:inline-flex; gap:8px; align-items:center; justify-content:center; height:52px; padding:0 22px; background:#2DADFF; color:#fff; border-radius:8px; border:1px solid #2DADFF; }
</style>

<section class="pricing-hero">

  <h1>Simple pricing for every learner</h1>
  <p>Choose a plan that matches your goals. Switch or cancel anytime. Save more with annual billing.</p>

  <!-- Billing Toggle -->
  <div class="billing-toggle">
    <span>Monthly</span>
    <label>
      <input id="billYearly" type="checkbox" />
      <div class="switch"><div class="knob"></div></div>
    </label>
    <span>Yearly</span>
    <span class="save">Save up to 20%</span>
  </div>
</section>

<!-- Plans -->
<section class="rwu-pricing">
  <div class="wrap">
    <div class="pill">Choose Your Plan</div>
    <!-- <h2 class="title">Transparent <span style="color:#1D9CFD">pricing</span>, real value</h2>
    <div class="sub">All plans include access to expert-led courses and AI-assisted practice features.</div> -->

    <div class="grid" id="plansGrid">
      <?php foreach ($plans as $p): ?>
      <article class="rwu-plan<?= $p['is_popular'] ? ' is-popular' : '' ?>" data-month="<?= (float)$p['price_month'] ?>" data-year="<?= (float)$p['price_year'] ?>">
        <?php if ($p['is_popular']): ?><div class="badge-pop">Most Popular</div><?php endif; ?>
        <h3 class="rwu-plan__name"><?= htmlspecialchars($p['name']) ?></h3>
        <div class="rwu-plan__tag"><?= htmlspecialchars($p['tag']) ?></div>

        <div class="rwu-price">
          <div class="rwu-price__currency"><?= $symbolLeft ?></div>
          <div class="rwu-price__amount js-amount"><?= number_format((float)$p['price_month'], 0) ?></div>
          <div class="rwu-price__currency"><?= $symbolRight ?></div>
          <div class="rwu-price__period js-period">/month</div>
        </div>

        <ul class="rwu-list">
          <?php foreach ($p['features'] as $f): ?>
            <li class="rwu-li">
              <span class="rwu-bullet rwu-bullet--ok">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M7.5 13.5l-3-3 1.4-1.4 1.6 1.6 4.9-4.9 1.4 1.4z" fill="currentColor"/></svg>
              </span>
              <span class="rwu-li__text"><?= htmlspecialchars($f) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <a class="rwu-plan__cta" href="<?= htmlspecialchars($p['cta_url']) ?>">
          Get Started
          <svg viewBox="0 0 24 24" width="18" height="18"><path d="M7 4l10 8-10 8V4z" fill="currentColor"/></svg>
        </a>
        <div class="rwu-plan__fine">No contracts. Cancel anytime.</div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Comparison -->
<section class="section-wrap">
  <h2 class="title" style="text-align:center;margin-bottom:14px;color:#0A033C;">Compare plans</h2>
  <div class="compare">
    <table>
      <thead>
        <tr>
          <th>Feature</th>
          <?php foreach ($plans as $p): ?>
            <th><?= htmlspecialchars($p['name']) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <tr><td>Unlimited courses</td><?php foreach ($plans as $i=>$p): ?><td class="<?= $i>0?'yes':'no' ?>"><?= $i>0?'Yes':'No' ?></td><?php endforeach; ?></tr>
        <tr><td>AI practice quizzes (advanced)</td><?php foreach ($plans as $i=>$p): ?><td class="<?= $i>=1?'yes':'no' ?>"><?= $i>=1?'Yes':'No' ?></td><?php endforeach; ?></tr>
        <tr><td>Assignments & feedback</td><?php foreach ($plans as $i=>$p): ?><td class="<?= $i>=1?'yes':'no' ?>"><?= $i>=1?'Yes':'No' ?></td><?php endforeach; ?></tr>
        <tr><td>Progress analytics</td><?php foreach ($plans as $i=>$p): ?><td class="<?= $i==2?'yes':'no' ?>"><?= $i==2?'Yes':'No' ?></td><?php endforeach; ?></tr>
        <tr><td>Priority/Dedicated support</td><?php foreach ($plans as $i=>$p): ?><td class="<?= $i==2?'yes':'no' ?>"><?= $i==2?'Yes':'No' ?></td><?php endforeach; ?></tr>
      </tbody>
    </table>
  </div>
</section>

<!-- FAQ -->
<section class="section-wrap faq">
  <h2 class="title" style="text-align:center;margin-bottom:10px;color:#0A033C;">Frequently asked questions</h2>
  <div class="q">Can I switch between monthly and yearly later?</div>
  <div class="a">Yes. You can switch at any time from your account billing page. We prorate automatically.</div>

  <div class="q">Do you offer refunds?</div>
  <div class="a">We provide a 7-day money-back guarantee on new subscriptions. Cancel within 7 days for a full refund.</div>

  <div class="q">Is there a plan for schools?</div>
  <div class="a">Yes. The Teams plan includes admin controls and analytics. Contact us to tailor for your institution.</div>
</section>

<!-- Final CTA -->
<section class="cta-final">
  <h3 style="color:#0A033C;margin-bottom:10px;">Ready to level up?</h3>
  <p style="color:#5F6C76;margin-bottom:18px;">Start today—cancel anytime.</p>
  <a class="btn" href="<?= MyUtility::makeUrl('Signup') ?>">
    Get Started
    <svg viewBox="0 0 24 24" width="18" height="18"><path d="M7 4l10 8-10 8V4z" fill="currentColor"/></svg>
  </a>
</section>

<script>
// Monthly / Yearly toggle (pure front-end)
(function(){
  const chk = document.getElementById('billYearly');
  const cards = document.querySelectorAll('.rwu-plan');

  function render(){
    cards.forEach(card => {
      const amt = card.querySelector('.js-amount');
      const period = card.querySelector('.js-period');
      const m = parseFloat(card.dataset.month || '0');
      const y = parseFloat(card.dataset.year || '0');
      if (chk.checked) {
        amt.textContent = (y).toFixed(0);
        period.textContent = '/year';
      } else {
        amt.textContent = (m).toFixed(0);
        period.textContent = '/month';
      }
    });
  }
  chk.addEventListener('change', render);
  render();

  // Tiny FAQ toggle
  document.querySelectorAll('.faq .q').forEach(q => {
    q.addEventListener('click', () => {
      const a = q.nextElementSibling;
      a.style.display = a.style.display === 'block' ? 'none' : 'block';
    });
  });
})();
</script>
