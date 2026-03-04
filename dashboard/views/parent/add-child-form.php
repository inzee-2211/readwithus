<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #2dadff 0%, #1a9fff 100%);
        --surface-glass: rgba(255, 255, 255, 0.7);
        --border-glass: rgba(255, 255, 255, 0.3);
        --shadow-premium: 0 20px 40px rgba(0, 0, 0, 0.05);
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-container {
        max-width: 900px;
        margin: 60px auto;
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .page-title {
        font-size: 3rem;
        font-weight: 900;
        color: var(--text-main);
        margin-bottom: 40px;
        text-align: center;
        letter-spacing: -0.04em;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 32px;
        margin-bottom: 40px;
    }

    .option-card-premium {
        background: #fff;
        border-radius: 32px;
        border: 2px solid #f1f5f9;
        padding: 40px 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-premium);
    }

    .option-card-premium:hover {
        transform: translateY(-12px);
        border-color: #2dadff;
        box-shadow: 0 30px 60px rgba(45, 173, 255, 0.15);
    }

    .option-card-premium i {
        font-size: 3.5rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 24px;
        display: block;
    }

    .option-card-premium h4 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }

    .option-card-premium p {
        color: var(--text-muted);
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
    }

    .form-card-glass {
        background: #fff;
        border-radius: 40px;
        border: 1px solid #f1f5f9;
        padding: 60px;
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.08);
        display: none;
    }

    .form-card-glass.active {
        display: block;
        animation: fadeInUp 0.5s ease-out;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 24px;
    }

    .form-header h4 {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-main);
        letter-spacing: -0.03em;
    }

    .btn-change-option {
        color: #ef4444;
        font-weight: 700;
        text-decoration: none !important;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 12px;
        background: #fef2f2;
        transition: all 0.2s;
    }

    .btn-change-option:hover {
        background: #fee2e2;
        transform: translateX(-4px);
    }

    .modern-form label {
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 12px;
        display: block;
        font-size: 0.9rem;
        letter-spacing: 0.02em;
    }

    .modern-form input,
    .modern-form select {
        width: 100%;
        padding: 16px 20px;
        border-radius: 18px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        transition: all 0.3s;
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-main);
    }

    .modern-form input:focus {
        border-color: #2dadff;
        background: #fff;
        box-shadow: 0 0 0 5px rgba(45, 173, 255, 0.1);
        outline: none;
    }

    .btn-premium {
        width: 100%;
        padding: 18px;
        border-radius: 18px;
        font-weight: 800;
        font-size: 1.125rem;
        background: var(--primary-gradient);
        color: #fff !important;
        border: none;
        cursor: pointer;
        box-shadow: 0 15px 30px rgba(45, 173, 255, 0.3);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-premium:hover {
        transform: scale(1.02);
        box-shadow: 0 20px 40px rgba(45, 173, 255, 0.4);
    }

    .btn-premium:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Typewriter Overlay Modernized */
    .typewriter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.95);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(12px);
    }

    .typewriter-box {
        background: #0f172a;
        color: #fff;
        padding: 60px;
        border-radius: 40px;
        max-width: 700px;
        width: 90%;
        border: 1px solid rgba(45, 173, 255, 0.3);
        box-shadow: 0 0 100px rgba(45, 173, 255, 0.2);
        position: relative;
    }

    .typewriter-text {
        font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
        font-size: 1.35rem;
        line-height: 1.8;
        min-height: 200px;
        color: #38bdf8;
    }

    .typewriter-cursor {
        display: inline-block;
        width: 12px;
        height: 1.35rem;
        background: #38bdf8;
        margin-left: 8px;
        animation: blink 0.8s infinite;
        vertical-align: middle;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }
    }

    .back-to-family {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-top: 40px;
        color: var(--text-muted);
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        gap: 10px;
    }

    .back-to-family:hover {
        color: var(--text-main);
        transform: translateX(-4px);
    }

    /* Make sure the submit renders like a real button (input submit + button) */
    .modern-form input[type="submit"].btn-premium,
    .modern-form button.btn-premium {
        -webkit-appearance: none;
        appearance: none;
        display: block;
        width: 100%;
        opacity: 1 !important;
        color: #fff !important;
        background: linear-gradient(135deg, #2dadff 0%, #1a9fff 100%) !important;
        border: none !important;
    }

    /* If it is disabled, keep it visible (still look disabled but not invisible) */
    .modern-form input[type="submit"].btn-premium:disabled,
    .modern-form button.btn-premium:disabled,
    .modern-form input[type="submit"].btn-premium[disabled],
    .modern-form button.btn-premium[disabled] {
        opacity: 0.6 !important;
        /* visible */
        filter: none !important;
        /* remove “blur/fade” look from some themes */
        cursor: not-allowed;
        box-shadow: 0 4px 12px rgba(45, 173, 255, 0.25) !important;
    }
</style>

<div class="container container--fixed">
    <div class="form-container">
        <h1 class="page-title"><?php echo Label::getLabel('LBL_EXPAND_YOUR_FAMILY'); ?></h1>

        <!-- Option Selection -->
        <div class="option-grid" id="optionSelector">
            <div class="option-card-premium" onclick="showForm('signup')">
                <i class="ion-person-add"></i>
                <h4><?php echo Label::getLabel('LBL_CREATE_NEW_CHILD'); ?></h4>
                <p><?php echo Label::getLabel('LBL_SIGNUP_YOUR_CHILD_DIRECTLY_AND_START_LEARNING'); ?></p>
            </div>
            <div class="option-card-premium" onclick="showForm('link')">
                <i class="ion-link"></i>
                <h4><?php echo Label::getLabel('LBL_LINK_EXISTING_USER'); ?></h4>
                <p><?php echo Label::getLabel('LBL_CONNECT_WITH_A_CHILD_WHO_ALREADY_HAS_AN_ACCOUNT'); ?></p>
            </div>
            <div class="option-card-premium" onclick="startManualInstructions()">
                <i class="ion-help-buoy"></i>
                <h4><?php echo Label::getLabel('LBL_MANUAL_GUIDE'); ?></h4>
                <p><?php echo Label::getLabel('LBL_HELP_ME_SIGNUP_MY_CHILD_MANUALLY'); ?></p>
            </div>
        </div>

        <!-- Direct Signup Form -->
        <div class="form-card-glass" id="signupFormSection">
            <div class="form-header">
                <h4><?php echo Label::getLabel('LBL_SIGNUP_YOUR_CHILD'); ?></h4>
                <a href="javascript:void(0)" class="btn-change-option" onclick="resetView()">
                    <i class="ion-arrow-left-c"></i> <?php echo Label::getLabel('LBL_BACK'); ?>
                </a>
            </div>
            <div class="modern-form">
                <?php
                $signupFrm->setFormTagAttribute('onsubmit', 'setupDirectSignup(this); return false;');
                $signupFrm->getField('submit')->addFieldTagAttribute('class', 'btn-premium');
                echo $signupFrm->getFormHtml();
                ?>
            </div>
        </div>

        <!-- Link Existing Form -->
        <div class="form-card-glass" id="linkFormSection">
            <div class="form-header">
                <h4><?php echo Label::getLabel('LBL_LINK_PROFILE'); ?></h4>
                <a href="javascript:void(0)" class="btn-change-option" onclick="resetView()">
                    <i class="ion-arrow-left-c"></i> <?php echo Label::getLabel('LBL_BACK'); ?>
                </a>
            </div>
            <div class="modern-form">
                <?php
                $linkFrm->setFormTagAttribute('onsubmit', 'setupLinkChild(this); return false;');
                $linkFrm->getField('submit')->addFieldTagAttribute('class', 'btn-premium');
                echo $linkFrm->getFormHtml();
                ?>
            </div>
        </div>

        <a class="back-to-family"
            href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
            <i class="ion-android-arrow-back"></i> <?php echo Label::getLabel('LBL_BACK_TO_FAMILY'); ?>
        </a>
    </div>
</div>

<!-- Typewriter Overlay Modernized -->
<div class="typewriter-overlay" id="manualOverlay">
    <div class="typewriter-box">
        <div class="typewriter-text" id="typewriterTarget"></div><span class="typewriter-cursor"></span>
        <div class="typewriter-bottom text-center mt-5" id="typewriterBtn" style="display: none;">
            <button class="btn-premium" style="width: auto; padding: 16px 48px; border-radius: 14px;"
                onclick="redirectToSignup()">
                <?php echo Label::getLabel('LBL_START_MANUAL_REGISTRATION'); ?>
            </button>
            <button class="btn btn-link text-white mt-4 d-block mx-auto fw-bold" onclick="closeOverlay()">
                <?php echo Label::getLabel('LBL_CANCEL'); ?>
            </button>
        </div>
    </div>
</div>

<script>
    function showForm(type) {
        $('#optionSelector').fadeOut(300, function () {
            if (type === 'signup') {
                $('#signupFormSection').addClass('active');
            } else {
                $('#linkFormSection').addClass('active');
            }
        });
    }

    function resetView() {
        $('.form-card-glass').removeClass('active').hide();
        $('#optionSelector').fadeIn(400);
    }

    function setupDirectSignup(frm) {
        if (!$(frm).validate()) return;
        fcom.updateWithAjax(fcom.makeUrl('Parent', 'setupChildSignup'), fcom.frmData(frm), function (res) {
            if (res.status) window.location.href = fcom.makeUrl('Parent', 'children');
        });
    }

    function setupLinkChild(frm) {
        if (!$(frm).validate()) return;
        fcom.updateWithAjax(fcom.makeUrl('Parent', 'setupAddChild'), fcom.frmData(frm), function (res) {
            if (res.status) window.location.href = fcom.makeUrl('Parent', 'children');
        });
    }

    /* Typewriter Logic */
    const instructions = "> INITIALIZING MANUAL GUIDE...\n\n1. Open the Registration Form.\n2. Create account using Child's details.\n3. Log in to Child's Profile.\n4. Go to 'Parent Requests' Section.\n5. Click 'APPROVE' on your request.\n\nSYSTEM READY.";
    let charIndex = 0;
    function startManualInstructions() {
        $('#manualOverlay').css('display', 'flex').hide().fadeIn(400);
        charIndex = 0;
        $('#typewriterTarget').text('');
        $('#typewriterBtn').hide();
        typeNext();
    }

    function typeNext() {
        if (charIndex < instructions.length) {
            let char = instructions.charAt(charIndex);
            if (char === '\n') {
                $('#typewriterTarget').append('<br>');
            } else {
                $('#typewriterTarget').append(char);
            }
            charIndex++;
            setTimeout(typeNext, 25);
        } else {
            $('#typewriterBtn').fadeIn(600);
        }
    }

    function closeOverlay() {
        $('#manualOverlay').fadeOut(300);
    }

    function redirectToSignup() {
        window.location.href = '<?php echo MyUtility::makeUrl('GuestUser', 'registrationForm', [], CONF_WEBROOT_URL_TRADITIONAL); ?>';
    }
</script>