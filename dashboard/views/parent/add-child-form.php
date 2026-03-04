<style>
    .form-container {
        max-width: 800px;
        margin: 40px auto;
    }

    .page-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 30px;
        text-align: center;
        background: linear-gradient(135deg, #2dadff 0%, #153e7d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }

    .option-card {
        background: #fff;
        border-radius: 20px;
        border: 2px solid #e2e8f0;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .option-card:hover {
        transform: translateY(-8px);
        border-color: #2dadff;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .option-card.active {
        border-color: #2dadff;
        background: #f0f9ff;
    }

    .option-card i {
        font-size: 3rem;
        color: #2dadff;
        margin-bottom: 15px;
        display: block;
    }

    .option-card h4 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 10px;
    }

    .option-card p {
        color: #718096;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }

    .form-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 40px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        display: none;
    }

    .form-card.active {
        display: block;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-header h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
    }

    .modern-form .row {
        margin-bottom: 20px;
    }

    .modern-form label {
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        display: block;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .modern-form input,
    .modern-form select {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .modern-form input:focus {
        border-color: #2dadff;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(45, 173, 255, 0.1);
        outline: none;
    }

    .btn-premium {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        font-weight: 800;
        background: linear-gradient(135deg, #2dadff 0%, #1a9fff 100%);
        color: #fff;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(45, 173, 255, 0.3);
        transition: all 0.2s;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(45, 173, 255, 0.4);
    }

    /* Typewriter Overlay */
    .typewriter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.9);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
    }

    .typewriter-box {
        background: #1a202c;
        color: #fff;
        padding: 40px;
        border-radius: 24px;
        max-width: 600px;
        width: 90%;
        border: 1px solid #2dadff;
        box-shadow: 0 0 50px rgba(45, 173, 255, 0.3);
        position: relative;
    }

    .typewriter-text {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.25rem;
        line-height: 1.6;
        min-height: 150px;
        color: #2dadff;
    }

    .typewriter-cursor {
        display: inline-block;
        width: 10px;
        height: 1.25rem;
        background: #2dadff;
        margin-left: 5px;
        animation: blink 1s infinite;
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

    .typewriter-btn {
        margin-top: 30px;
        display: none;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-top: 30px;
        color: #64748b;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }

    .back-link:hover {
        color: #2dadff;
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
            <div class="option-card" onclick="showForm('signup')">
                <i class="ion-person-add"></i>
                <h4><?php echo Label::getLabel('LBL_CREATE_NEW_CHILD'); ?></h4>
                <p><?php echo Label::getLabel('LBL_SIGNUP_YOUR_CHILD_DIRECTLY_AND_START_LEARNING'); ?></p>
            </div>
            <div class="option-card" onclick="showForm('link')">
                <i class="ion-link"></i>
                <h4><?php echo Label::getLabel('LBL_LINK_EXISTING_USER'); ?></h4>
                <p><?php echo Label::getLabel('LBL_CONNECT_WITH_A_CHILD_WHO_ALREADY_HAS_AN_ACCOUNT'); ?></p>
            </div>
            <div class="option-card" onclick="startManualInstructions()">
                <i class="ion-help-buoy"></i>
                <h4><?php echo Label::getLabel('LBL_MANUAL_GUIDE'); ?></h4>
                <p><?php echo Label::getLabel('LBL_HELP_ME_SIGNUP_MY_CHILD_MANUALLY'); ?></p>
            </div>
        </div>

        <!-- Direct Signup Form -->
        <div class="form-card" id="signupFormSection">
            <div class="form-header">
                <h4><?php echo Label::getLabel('LBL_SIGNUP_YOUR_CHILD'); ?></h4>
                <button class="btn btn-link btn-sm float-end"
                    onclick="resetView()"><?php echo Label::getLabel('LBL_CHANGE_OPTION'); ?></button>
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
        <div class="form-card" id="linkFormSection">
            <div class="form-header">
                <h4><?php echo Label::getLabel('LBL_SEND_CONNECTION_REQUEST'); ?></h4>
                <button class="btn btn-link btn-sm float-end"
                    onclick="resetView()"><?php echo Label::getLabel('LBL_CHANGE_OPTION'); ?></button>
            </div>
            <div class="modern-form">
                <?php
                $linkFrm->setFormTagAttribute('onsubmit', 'setupLinkChild(this); return false;');
                $linkFrm->getField('submit')->addFieldTagAttribute('class', 'btn-premium');
                echo $linkFrm->getFormHtml();
                ?>
            </div>
        </div>

        <a class="back-link" href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
            <i class="ion-android-arrow-back me-2"></i> <?php echo Label::getLabel('LBL_BACK_TO_MY_FAMILY'); ?>
        </a>
    </div>
</div>

<!-- Typewriter Overlay -->
<div class="typewriter-overlay" id="manualOverlay">
    <div class="typewriter-box">
        <div class="typewriter-text" id="typewriterTarget"></div><span class="typewriter-cursor"></span>
        <div class="typewriter-btn text-center" id="typewriterBtn">
            <button class="btn btn-primary btn-lg rounded-pill px-5" onclick="redirectToSignup()">
                <?php echo Label::getLabel('LBL_OK_I_UNDERSTAND'); ?>
            </button>
            <button class="btn btn-link text-white mt-2 d-block mx-auto" onclick="closeOverlay()">
                <?php echo Label::getLabel('LBL_CANCEL'); ?>
            </button>
        </div>
    </div>
</div>

<script>
    function showForm(type) {
        $('#optionSelector').fadeOut(200, function () {
            if (type === 'signup') {
                $('#signupFormSection').addClass('active');
            } else {
                $('#linkFormSection').active();
            }
        });
    }  // Fix for jQuery active() which doesn't exist standardly
    $.fn.active = function () { return this.addClass('active'); };

    function resetView() {
        $('.form-card').removeClass('active');
        $('#optionSelector').fadeIn(300);
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
    const instructions = "Hello! To register your child manually, please follow these steps:\n\n1. Go to the general signup page.\n2. Register using your child's email.\n3. You will be logged into their profile.\n4. Go to 'Parent Requests' and click ACCEPT on your name.\n\nReady to go?";
    let charIndex = 0;
    function startManualInstructions() {
        $('#manualOverlay').css('display', 'flex').hide().fadeIn(300);
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
            setTimeout(typeNext, 30);
        } else {
            $('#typewriterBtn').fadeIn(500);
        }
    }

    function closeOverlay() {
        $('#manualOverlay').fadeOut(300);
    }

    function redirectToSignup() {
        window.location.href = '<?php echo MyUtility::makeUrl('GuestUser', 'registrationForm', [], CONF_WEBROOT_URL_TRADITIONAL); ?>';
    }
</script>