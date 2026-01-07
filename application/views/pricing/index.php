<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
function asset_css($file){
  $abs = CONF_APPLICATION_PATH . 'public/' . ltrim($file,'/');
  return CONF_WEBROOT_URL . $file . '?v=' . (@filemtime($abs) ?: time());
}
$symbolLeft  = $siteCurrency['currency_symbol_left'] ?? '';
$symbolRight = $siteCurrency['currency_symbol_right'] ?? '';
$levels          = $levels ?? [];
$selectedLevelId = $selectedLevelId ?? 0;
$hasActiveSubscription = $hasActiveSubscription ?? false;
$hasQuizAccess        = $hasQuizAccess ?? false;
$currentQuizPackageId = $currentQuizPackageId ?? 0;
$quizAccessStatus     = $quizAccessStatus ?? '';

$currentPackageId      = $currentPackageId ?? 0;
$userDetail = $userDetail ?? [];



// Normalize $plans:
// - If controller already mapped (name, price_month, price_year...), this is a no-op.
// - If controller passed DB rows (spackage_*), map them to your old shape.
if (isset($plans) && is_array($plans)) {
  // First pass: map fields if needed
  foreach ($plans as $idx => $p) {
    if (isset($p['spackage_id'])) {
      // preserve flags from controller
      $isCurrent = !empty($p['is_current']);
      $isUpgrade = !empty($p['is_upgrade']);

  $isQuizOnly = !empty($p['spackage_is_quiz_only']);

      $plans[$idx] = [
        'id'          => (int)$p['spackage_id'],
        'name'        => (string)$p['spackage_name'],
        'tag'         => (string)($p['spackage_description'] ?? ''),
        'price_month' => (float)$p['spackage_price_monthly'],
        'price_year'  => (float)$p['spackage_price_yearly'],
         'trial_days'  => isset($p['spackage_trial_days']) ? (int)$p['spackage_trial_days'] : 0,
            'is_quiz_only' => $isQuizOnly,
       'features'    => $isQuizOnly ? [
      'Unlimited practice quizzes',
      'User support via email',
      'No course/subject access',
  ] : [
      'Access to ' . (int)$p['spackage_subject_limit'] . ' subjects',
      'Unlimited courses in selected subjects',
      'Email/priority support',
  ],

  'is_popular'  => false,
  'is_current'  => !empty($p['is_current']),
  'is_upgrade'  => !empty($p['is_upgrade']),
];

    } else {
      // ensure expected keys exist for seeded data
      $plans[$idx]['id']          = $plans[$idx]['id']          ?? ($idx+1);
      $plans[$idx]['name']        = $plans[$idx]['name']        ?? ('Plan '.($idx+1));
      $plans[$idx]['tag']         = $plans[$idx]['tag']         ?? '';
      $plans[$idx]['price_month'] = (float)($plans[$idx]['price_month'] ?? 0);
      $plans[$idx]['price_year']  = (float)($plans[$idx]['price_year'] ?? ($plans[$idx]['price_month']*12));
      $plans[$idx]['features']    = $plans[$idx]['features']    ?? [];
        $plans[$idx]['trial_days']  = (int)($plans[$idx]['trial_days'] ?? 0);
      $plans[$idx]['is_popular']  = (bool)($plans[$idx]['is_popular'] ?? false);
      $plans[$idx]['is_current']  = (bool)($plans[$idx]['is_current'] ?? false);
      $plans[$idx]['is_upgrade']  = (bool)($plans[$idx]['is_upgrade'] ?? false);
      $plans[$idx]['is_quiz_only'] = (bool)($plans[$idx]['is_quiz_only'] ?? false);

    }
  }

  // Second pass: mark a "Most Popular" if none specified (pick middle when 3 plans)
  $anyPopular = false;
  foreach ($plans as $p) { if (!empty($p['is_popular'])) { $anyPopular = true; break; } }
  if (!$anyPopular && count($plans) >= 3) {
    $plans[1]['is_popular'] = true; // middle plan
  }

  // Third pass: add monthly/yearly checkout URLs (keeps your single CTA; JS switches href)
  foreach ($plans as $idx => $p) {
    // $plans[$idx]['cta_month_url'] = MyUtility::makeUrl('Subscription', 'selectSubjects', [ (int)$p['id'], 'monthly' ]);
    // $plans[$idx]['cta_year_url']  = MyUtility::makeUrl('Subscription', 'selectSubjects', [ (int)$p['id'], 'yearly' ]);
$isQuizOnly = !empty($p['is_quiz_only']);

if ($isQuizOnly) {
    $plans[$idx]['cta_month_url'] = MyUtility::makeUrl('Subscription', 'activateFreeQuizPlan', [(int)$p['id']]);
    $plans[$idx]['cta_year_url']  = $plans[$idx]['cta_month_url']; // same (no billing)
} else {
    $plans[$idx]['cta_month_url'] = MyUtility::makeUrl('Subscription', 'selectSubjects', [(int)$p['id'], 'monthly']);
    $plans[$idx]['cta_year_url']  = MyUtility::makeUrl('Subscription', 'selectSubjects', [(int)$p['id'], 'yearly']);
}

  }
}

?>
<link rel="stylesheet" href="<?= asset_css('css/home.pricing.css') ?>">

<style>
  .pricing-hero{
  background: linear-gradient(180deg,#F5FAFF 0%,#FFFFFF 100%);
  padding: 64px 16px 28px;
  text-align:center;
}
/* Level selector pill (matches ReadWithUs style) */
.level-filter {
  margin: 24px auto 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.level-filter label {
  font-size: 17px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #6B7280;          /* soft grey like filters */
  font-weight: 700;
}
.rwu-plan__cta.is-disabled{
  opacity: .55;
  pointer-events: none;
  cursor: not-allowed;
}

/* -------- LEVEL TABS -------- */
.level-tabs {
 
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 32px;
  margin: 22px 0 10px;
  padding-bottom: 4px;
  border-bottom: 1px solid #e5e7eb;
}

.level-tab {
  font-size: 16px;
  color: #6b7280;
  font-weight: 500;
  padding: 8px 4px;
  text-decoration: none;
  position: relative;
  transition: color 0.2s ease;
}

.level-tab:hover {
  color: #1d9cfd;
}

.level-tab.active {
   background: #F5FAFF;
  color: #1d9cfd;
  font-weight: 600;
}

.level-tab.active::after {
  content: "";
  position: absolute;
  bottom: -5px;
  left: 0;
  right: 0;
  margin: auto;
  width: 40px;
  height: 3px;
  background-color: #1d9cfd;
  border-radius: 2px;
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
/* ✅ Center plans when only 1-2 cards exist */
#plansGrid{
  display:flex !important;
  justify-content:center;
  flex-wrap:wrap;
  gap:24px;
}
#plansGrid .rwu-plan{
  width:360px;
  max-width:100%;
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
  
  <div class="level-filter">
  <?php if (!empty($levels)): ?>
    <label for="levelSelect">Select your level</label>
    <div class="level-tabs">
      
  <?php foreach ($levels as $lvl): ?>
    <?php 
      $active = ($selectedLevelId == $lvl['id']) ? 'active' : '';
      $url = MyUtility::makeUrl('Pricing', null, [], CONF_WEBROOT_FRONT_URL) . '?level_id=' . (int)$lvl['id'];
    ?>
    <a href="<?= $url ?>" class="level-tab <?= $active ?>">
      <?= htmlspecialchars($lvl['level_name']) ?>
    </a>
  <?php endforeach; ?>
</div>
</div>
<?php endif; ?>


  <div class="billing-toggle">
    <span>Monthly</span>
    <label>
      <input id="billYearly" type="checkbox" />
      <div class="switch"><div class="knob"></div></div>
    </label>
    <span>Yearly</span>
    <span class="save">Save up to 10%</span>
  </div>
</section>


<!-- Plans -->
<section class="rwu-pricing">
  <div class="wrap">
    <div class="pill">Choose Your Plan</div>

    <div class="grid" id="plansGrid">
      <?php foreach ($plans as $p): ?>
      <article
        class="rwu-plan<?= !empty($p['is_popular']) ? ' is-popular' : '' ?>"
        data-month="<?= (float)$p['price_month'] ?>"
        data-year="<?= (float)$p['price_year'] ?>"
        data-month-url="<?= htmlspecialchars($p['cta_month_url']) ?>"
        data-year-url="<?= htmlspecialchars($p['cta_year_url']) ?>"
      >
        <?php if (!empty($p['is_popular'])): ?><div class="badge-pop">Most Popular</div><?php endif; ?>
        <h3 class="rwu-plan__name"><?= htmlspecialchars($p['name']) ?></h3>
        <?php if (!empty($p['tag'])): ?><div class="rwu-plan__tag"><?= htmlspecialchars($p['tag']) ?></div><?php endif; ?>

        <div class="rwu-price">
          <div class="rwu-price__currency"><?= $symbolLeft ?></div>
          <div class="rwu-price__amount js-amount"><?= number_format((float)$p['price_month'], 0) ?></div>
          <div class="rwu-price__currency"><?= $symbolRight ?></div>
          <div class="rwu-price__period js-period">/month</div>
        </div>

        <ul class="rwu-list">
          <?php foreach (($p['features'] ?? []) as $f): ?>
            <li class="rwu-li">
              <span class="rwu-bullet rwu-bullet--ok">
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M7.5 13.5l-3-3 1.4-1.4 1.6 1.6 4.9-4.9 1.4 1.4z" fill="currentColor"/></svg>
              </span>
              <span class="rwu-li__text"><?= htmlspecialchars($f) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- Single CTA that switches href when billing toggle changes -->
           <?php
$hasSub      = !empty($hasActiveSubscription);
$planId      = (int)($p['id'] ?? 0);
$isQuizOnly  = !empty($p['is_quiz_only']);

// ✅ Strong current-plan detection using controller vars
$isCurrent = !empty($p['is_current']) || ($hasSub && (int)$currentPackageId === $planId);
$isQuizCurrent = (!empty($hasQuizAccess) && (int)$currentQuizPackageId === $planId);


// ✅ If controller didn't provide upgrade flag, decide upgrade by price
$isUpgrade = !empty($p['is_upgrade']);
if ($hasSub && !$isCurrent && !$isUpgrade) {
    // Simple heuristic: higher monthly price = upgrade
    $isUpgrade = ((float)$p['price_month'] > 0); // keeps paid as upgrade vs free
}

  // Defaults for guests / no active subscription
  $ctaLabel = 'Get Started';
  $ctaHref  = htmlspecialchars($p['cta_month_url']);
  $ctaClass = 'rwu-plan__cta js-cta'; // JS will switch month/year URL
  $fineText = 'No contracts. Cancel anytime.';

  // 🔹 Trial context
  $isLogged          = UserAuth::isUserLogged();
  $userTrialEligible = !empty($userDetail['user_trial_eligible'] ?? 0);
  $trialDays         = (int)($p['trial_days'] ?? 0);

  // Show trial only if:
  // - user is logged in
  // - has NO active subscription yet
  // - this package has trial_days > 0
  // - admin/system still allows trial for this user
  $canStartTrial = $isLogged && !$hasSub && $trialDays > 0 && $userTrialEligible;
if ($hasSub && $isCurrent) {

    $ctaLabel = 'Current plan';
    $ctaHref  = MyUtility::makeUrl('Courses');
    $ctaClass = 'rwu-plan__cta'; // no js-cta
    $fineText = $isQuizOnly
        ? 'Free quiz plan is active.'
        : 'Your subscription is active.';

}
 elseif ($isQuizOnly && $isQuizCurrent) {
    // ✅ user has free quiz plan active (but no paid subscription)
    $ctaLabel = 'Quizzes active';
    // $ctaHref  = MyUtility::makeUrl('Quizizz'); // or Courses
    $ctaClass = 'rwu-plan__cta';
    $fineText = 'Free quiz plan is active' . ($quizAccessStatus ? " ({$quizAccessStatus})" : '') . '.';
  } elseif ($hasSub) {

    // ✅ User has an active subscription

    if ($isCurrent) {
        $ctaLabel = 'Current plan';
        $ctaHref  = MyUtility::makeUrl('Courses');
        $ctaClass = 'rwu-plan__cta';
        $fineText = 'Your subscription is active.';
    } elseif ($isQuizOnly) {
        // ✅ Free quizzes are INCLUDED in paid plans
        $ctaLabel = 'Included with your plan';
        $ctaHref  = MyUtility::makeUrl('Courses');
        $ctaClass = 'rwu-plan__cta is-disabled';
        $fineText = 'Quizzes are already unlocked in your subscription.';
    } elseif ($isUpgrade) {
        $ctaLabel = 'Upgrade plan';
        $ctaHref  = htmlspecialchars($p['cta_month_url']);
        $ctaClass = 'rwu-plan__cta js-cta';
        $fineText = 'Upgrade anytime.';
    } else {
        // ✅ Not current + not upgrade = downgrade (hide/disable)
        $ctaLabel = 'Current plan is better';
        $ctaHref  = 'javascript:void(0)';
        $ctaClass = 'rwu-plan__cta is-disabled';
        $fineText = 'Downgrades are not available from here.';
    }
  }

?>


        <?php if ($hasSub && $isCurrent): ?>
          <div class="rwu-plan__status" style="margin-bottom:6px;color:#16a34a;font-weight:700;font-size:17px;">
            Your current plan
          </div>
        <?php endif; ?>

        <a class="<?= $ctaClass ?>" href="<?= $ctaHref ?>">
          <?= $ctaLabel ?>
          <svg viewBox="0 0 24 24" width="18" height="18"><path d="M7 4l10 8-10 8V4z" fill="currentColor"/></svg>
        </a>
        <div class="rwu-plan__fine"><?= $fineText; ?></div>

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
  <!-- Free quizzes: YES for everyone -->
  <tr>
    <td>Free quizzes</td>
    <?php foreach ($plans as $p): ?>
      <td class="yes">Yes</td>
    <?php endforeach; ?>
  </tr>

  <!-- AI tutor -->
  <tr>
    <td>AI tutor</td>
    <?php foreach ($plans as $p): ?>
      <?php $paid = empty($p['is_quiz_only']); ?>
      <td class="<?= $paid ? 'yes' : 'no' ?>"><?= $paid ? 'Yes' : 'No' ?></td>
    <?php endforeach; ?>
  </tr>

  <!-- Everything else: only for PAID plans (non-quiz-only) -->
  <tr>
    <td>Unlimited courses</td>
    <?php foreach ($plans as $p): ?>
      <?php $paid = empty($p['is_quiz_only']); ?>
      <td class="<?= $paid ? 'yes' : 'no' ?>"><?= $paid ? 'Yes' : 'No' ?></td>
    <?php endforeach; ?>
  </tr>

  <tr>
    <td>Assignments & feedback</td>
    <?php foreach ($plans as $p): ?>
      <?php $paid = empty($p['is_quiz_only']); ?>
      <td class="<?= $paid ? 'yes' : 'no' ?>"><?= $paid ? 'Yes' : 'No' ?></td>
    <?php endforeach; ?>
  </tr>

  <tr>
    <td>Progress analytics</td>
    <?php foreach ($plans as $p): ?>
      <?php $paid = empty($p['is_quiz_only']); ?>
      <td class="<?= $paid ? 'yes' : 'no' ?>"><?= $paid ? 'Yes' : 'No' ?></td>
    <?php endforeach; ?>
  </tr>

  <tr>
    <td>Priority/Dedicated support</td>
    <?php foreach ($plans as $p): ?>
      <?php $paid = empty($p['is_quiz_only']); ?>
      <td class="<?= $paid ? 'yes' : 'no' ?>"><?= $paid ? 'Yes' : 'No' ?></td>
    <?php endforeach; ?>
  </tr>
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
  <div class="a">We provide a 1-day money-back guarantee on new subscriptions. Cancel within 1 days for a full refund.</div>

  <div class="q">Is there a plan for schools?</div>
  <div class="a">Yes. The Teams plan includes admin controls and analytics. Contact us to tailor for your institution.</div>
</section>

<!-- Final CTA -->
<!-- <section class="cta-final">
  <h3 style="color:#0A033C;margin-bottom:10px;">Ready to level up?</h3>
  <p style="color:#5F6C76;margin-bottom:18px;">Start today—cancel anytime.</p>
  <a class="btn" href="<?= MyUtility::makeUrl('Signup') ?>">
    Get Started
    <svg viewBox="0 0 24 24" width="18" height="18"><path d="M7 4l10 8-10 8V4z" fill="currentColor"/></svg>
  </a>
</section> -->

<script>
// Monthly / Yearly toggle (pure front-end) + switch CTA hrefs
(function(){
  const chk = document.getElementById('billYearly');
  const cards = document.querySelectorAll('.rwu-plan');

  function render(){
    cards.forEach(card => {
      const amt = card.querySelector('.js-amount');
      const period = card.querySelector('.js-period');
      const cta = card.querySelector('.js-cta');
      const m = parseFloat(card.dataset.month || '0');
      const y = parseFloat(card.dataset.year || '0');
      const mUrl = card.dataset.monthUrl || '#';
      const yUrl = card.dataset.yearUrl || '#';

      if (chk.checked) {
        amt.textContent = (y || 0).toFixed(0);
        period.textContent = '/year';
        if (cta) cta.href = yUrl;
      } else {
        amt.textContent = (m || 0).toFixed(0);
        period.textContent = '/month';
        if (cta) cta.href = mUrl;
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
 const levelSelect = document.getElementById('levelSelect');
  if (levelSelect) {
    levelSelect.addEventListener('change', function () {
      const form = document.getElementById('levelFilterForm');
      if (form) form.submit();
    });
  }
</script>
