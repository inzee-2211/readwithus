/* global fcom, userIsTeacher, langLbl, userType, TEACHER */
var teachLangs = [];
$(document).ready(function () {
    profileInfoForm();
    $('body').on('click', '.tab-ul-js li a', function () {
        $('.tab-ul-js li').removeClass('is-active');
        $(this).parent('li').addClass('is-active');
    });
});
(function () {
    var dv = '#formBlock-js';
    var profileInfoFormDiv = '#profileInfoFrmBlock';
    changePasswordForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'changePasswordForm'), '', function (res) {
            $(dv).html(res);
        });
    };
    DeleteAccountForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'deleteAccount'), '', function (t) {
            $(dv).html(t);
        });
    };
    getProfileProgress = function () {
        if (userType != TEACHER) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Teacher', 'profileProgress'), '', function (data) {
            if (data && data.PrfProg) {
                tpp = data.PrfProg;
                $.each(tpp, function (key, value) {
                    switch (key) {
                        case 'isProfileCompleted':
                            if (value) {
                                $('.is-profile-complete-js').removeClass('infobar__media-icon--alert').addClass('infobar__media-icon--tick');
                                $('.is-profile-complete-js').html('');
                                $('.aside--progress--menu').addClass('is-completed');
                            } else {
                                $('.is-profile-complete-js').removeClass('infobar__media-icon--tick').addClass('infobar__media-icon--alert');
                                $('.is-profile-complete-js').html('!');
                            }
                            break;
                        case 'generalAvailabilityCount':
                            value = parseInt(value);
                            if (0 >= value) {
                                $('.general-availability-js').parent('li').removeClass('is-completed');
                                $('.availability-setting-js').removeClass('is-completed');
                            } else {
                                $('.general-availability-js').parent('li').addClass('is-completed');
                                $('.availability-setting-js').addClass('is-completed');
                            }
                            break;
                        case 'generalProfile':
                            value = parseInt(value);
                            if (0 >= value) {
                                $('.profile-Info-js').parent('li').removeClass('is-completed');
                            } else {
                                $('.profile-Info-js').parent('li').addClass('is-completed');
                            }
                            break;
                        case 'uqualificationCount':
                            value = parseInt(value);
                            if (0 >= value) {
                                $('.teacher-qualification-js').parent('li').removeClass('is-completed');
                            } else {
                                $('.teacher-qualification-js').parent('li').addClass('is-completed');
                            }
                            break;
                        case 'priceCount':
                            value = parseInt(value);
                            if (0 >= value) {
                                $('.teacher-tech-lang-price-js').parent('li').removeClass('is-completed');
                            } else {
                                $('.teacher-tech-lang-price-js').parent('li').addClass('is-completed');
                            }
                            break;
                        case 'languagesCount':
                            value = parseInt(value);
                            if (0 >= value) {
                                $('.teacher-lang-form-js').parent('li').removeClass('is-completed');
                            } else {
                                $('.teacher-lang-form-js').parent('li').addClass('is-completed');
                            }
                            break;
                        case 'preferenceCount':
                            value = parseInt(value);
                            if (0 >= value) {
                                $('.teacher-preferences-js').parent('li').removeClass('is-completed');
                            } else {
                                $('.teacher-preferences-js').parent('li').addClass('is-completed');
                            }
                            break;
                        case 'percentage':
                            $('.teacher-profile-progress-bar-js').attr("aria-valuenow", value);
                            value = value + "%";
                            $('.teacher-profile-progress-bar-js').css({ "width": value });
                            break;
                        case 'totalFilledFields':
                            $('.progress__step').removeClass('is-active');
                            for (let totalFilledFields = 0; totalFilledFields < value; totalFilledFields++) {
                                $('.progress__step').eq(totalFilledFields).addClass('is-active');
                            }
                            value = tpp.totalFilledFields + "/" + tpp.totalFields;
                            $('.progress-count-js').text(value);
                            if ((parseInt(tpp.isProfileCompleted) == 1) || (parseInt(tpp.totalFilledFields) == (parseInt(tpp.totalFields) - 1) && parseInt(tpp.generalAvailabilityCount) == 0)) {
                                $('.profile-setting-js').addClass('is-completed');
                            } else {
                                $('.profile-setting-js').removeClass('is-completed');
                            }
                            break;
                    }
                });
            }
        }, { fOutMode: 'json', process: false });
    };
    changeEmailForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'changeEmailForm'), '', function (t) {
            $(dv).html(t);
        });
    };
    bankInfoForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'bankInfoForm'), '', function (t) {
            $(dv).html(t);
        });
    };
    setUpBankInfo = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setUpBankInfo'), fcom.frmData(frm), function (t) { });
    };
    paypalEmailAddressForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'paypalEmailAddressForm'), '', function (t) {
            $(dv).html(t);
            $('#innerTabs > li').removeClass('is-active');
            $('#innerTabs > li:nth-child(2)').addClass('is-active');
        });
    };
    setupPaypalInfo = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setupPaypalInfo'), fcom.frmData(frm), function (t) { });
    };
    setupPassword = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setupPassword'), fcom.frmData(frm), function (t) {
            changePasswordForm();
        });
    };
    setupEmail = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setupEmail'), fcom.frmData(frm), function (t) {
            changeEmailForm();
        });
    };
    profileInfoForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'ProfileInfoForm'), '', function (response) {
            $(dv).html(response);
            if (userIsTeacher) {
                getProfileProgress();
            }
        });
    };
    setupProfileInfo = function (frm, gotoProfileImageForm) {
        if (!$(frm).validate()) {
            $("html, body").animate({ scrollTop: $(".error").eq(0).offset().top - 100 }, "slow");
            return false;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setupProfileInfo'), fcom.frmData(frm), function (t) {
            if (userIsTeacher) {
                getProfileProgress();
            }
            if (gotoProfileImageForm) {
                $('.profile-imag-li').click();
            }
            return true;
        });
    };
    teacherPreferencesForm = function () {
        fcom.ajax(fcom.makeUrl('Teacher', 'teacherPreferencesForm'), '', function (response) {
            $(dv).html(response);
            getProfileProgress();
        });
    };
    setupTeacherPreferences = function (frm, goAvailablityForm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Teacher', 'setupTeacherPreferences'), fcom.frmData(frm), function (t) {
            if (goAvailablityForm) {
                window.location = fcom.makeUrl('Teacher', 'availability');
                return;
            }
            getProfileProgress();
        });
    };
    teacherLanguagesForm = function () {
        fcom.ajax(fcom.makeUrl('Teacher', 'teacherLanguagesForm'), '', function (response) {
            $(dv).html(response);
            if (userIsTeacher) {
                getProfileProgress();
            }
        });
    };
    setupTeacherLanguages = function (frm, goToPriceForm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Teacher', 'setupTeacherLanguages'), fcom.frmData(frm), function (response) {
            if (goToPriceForm) {
                $('.teacher-tech-lang-price-js').trigger('click');
                return;
            }
            getProfileProgress();
        });
    };
    setPreferredDashboad = function (id) {
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setPrefferedDashboard', [id]), '', function (res) {
            if (userIsTeacher) {
                getProfileProgress();
            }
        });
    };
    changeProficiency = function (obj, langId) {
        langId = parseInt(langId);
        if (langId <= 0) {
            return;
        }
        let value = obj.value;
        slanguageSection = '.slanguage-' + langId;
        slanguageCheckbox = '.slanguage-checkbox-' + langId;
        if (value == '') {
            $(slanguageSection).find('.badge-js').remove();
            $(slanguageSection).removeClass('is-selected');
            $(slanguageCheckbox).prop('checked', false);
        } else {
            $(slanguageSection).addClass('is-selected');
            $(slanguageCheckbox).prop('checked', true);
            $(slanguageSection).find('.badge-js').remove();
            $(slanguageSection).find('.selection__trigger-label').append('<span class="badge color-secondary badge-js  badge--round badge--small margin-0">' + obj.selectedOptions[0].innerHTML + '</span>');
        }
    };
    techLangPriceForm = function (showAdminSlab) {
        showAdminSlab = showAdminSlab ? showAdminSlab : 0;
        var data = 'showAdminSlab=' + showAdminSlab;
        fcom.ajax(fcom.makeUrl('Teacher', 'techLangPriceForm'), data, function (response) {
            $(dv).html(response);
            if (userIsTeacher) {
                getProfileProgress();
            }
        });
    };
    setupLangPrice = function (frm, goToQualiForm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Teacher', 'setupLangPrice'), fcom.frmData(frm), function (t) {
            if (goToQualiForm) {
                $('.teacher-qualification-js').click();
            }
            getProfileProgress();
        });
    };
    teacherQualification = function () {
        fcom.ajax(fcom.makeUrl('Teacher', 'teacherQualification'), '', function (response) {
            $(dv).html(response);
            if (userIsTeacher) {
                getProfileProgress();
            }
        });
    };
    deleteAccount = function () {
        if (!confirm(langLbl.gdprDeleteAccDesc)) {
            return;
        }
        fcom.ajax(fcom.makeUrl('Account', 'deleteAccountForm'), '', function (response) {
            $.facebox(response);
        });
    };
    setUpGdprDelAcc = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setupGdprDeleteAcc'), fcom.frmData(frm), function (t) {
            $.facebox.close();
        });
    };
    teacherPreferences = function () {
        fcom.ajax(fcom.makeUrl('Teacher', 'teacherPreferences'), '', function (t) {
            $(dv).html(t);
        });
    };
    teacherQualificationForm = function (id) {
        fcom.ajax(fcom.makeUrl('Teacher', 'teacherQualificationForm', [id]), '', function (response) {
            $.facebox(response, 'facebox-medium');
        });
    };
    setupTeacherQualification = function (frm) {
        if (!$(frm).validate()) {
            return false;
        }
        var formData = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('Teacher', 'setupTeacherQualification'), formData, function (res) {
            teacherQualification();
            $.facebox.close();
        }, { fOutMode: 'json' });
    };
    deleteTeacherQualification = function (id) {
        if (confirm(langLbl['confirmRemove'])) {
            fcom.updateWithAjax(fcom.makeUrl('Teacher', 'deleteTeacherQualification', [id]), '', function (t) {
                $('#qualification-' + id).remove();
            });
        }
    };
    setupVideoLink = function (frm, goToLangForm) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setupVideoLink'), fcom.frmData(frm), function (t) {
            if (goToLangForm && $('.profile-lang-li').length > 0) {
                $('.profile-lang-li').first().click();
            }
        });
    };
    profileImageForm = function () {
        fcom.ajax(fcom.makeUrl('Account', 'profileImageForm'), '', function (response) {
            $(profileInfoFormDiv).html(response);
        });
    };
    removeProfileImage = function () {
        if (confirm(langLbl['confirmRemove'])) {
            fcom.updateWithAjax(fcom.makeUrl('Account', 'removeProfileImage'), '', function (t) {
                profileImageForm();
            });
        }

    };
    sumbmitProfileImage = function () {
        if (cropObj) {
            /* Add blob and file name */
            $image.cropper('getCroppedCanvas').toBlob(function (blob) {
                var formData = new FormData();
                formData.append("fIsAjax", 1);
                formData.append('user_profile_image', blob, 'file.jpg');
                fcom.ajaxMultipart(fcom.makeUrl('Account', 'setupProfileImage'), formData, function (res) {
                    profileImageForm();
                    $.facebox.close();
                    $image.cropper('destroy');
                }, { fOutMode: 'json' });
            }, 'image/jpeg', 0.9);
        }
    };
    $(document).on('click', '[data-method]', function () {
        var data = $(this).data();
        if (data.method) {
            result = $image.cropper(data.method, data.option);
        }
    });
    var $image = null;
    var cropObj = null;
    cropImage = function (obj) {
        if ($image) {
            $image.cropper('destroy');
        }
        $image = obj;
        cropObj = $image.cropper({
            aspectRatio: 1,
            guides: true,
            highlight: true,
            dragCrop: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            rotatable: true,
            responsive: true,
            built: function () {
                $(this).cropper("zoom", 0.5);
            }
        });
    };
    popupImage = function (input) {
        wid = $(window).width();
        wid = (wid > 767) ? 500 : 280;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $.facebox('<div class="popup__body"><div class="img-container"><img alt="Picture" src="' + e.target.result + '" class="img_responsive" id="new-img" /></div><div class="img-description"><div class="rotator-info">' + lblCroperInfoText + '</div><div class="-align-center rotator-actions"><a href="javascript:void(0)" class="btn btn--primary btn--sm" title="' + lblRL + '" data-option="-90" data-method="rotate">' + lblRL + '</a>&nbsp;<a onclick="sumbmitProfileImage();" href="javascript:void(0)" class="btn btn--secondary btn--sm">' + lblUpdatePic + '</a>&nbsp;<a href="javascript:void(0)" class="btn btn--primary btn--sm rotate-right" title="' + lblRR + '" data-option="90" data-method="rotate">' + lblRR + '</a></div></div></div>', '');
                $('#new-img').width(wid);
                cropImage($('#new-img'));
            };
            reader.readAsDataURL(input.files[0]);
        }
        input.value = '';
    };
    getLangProfileInfoForm = function (id) {
        fcom.ajax(fcom.makeUrl('Account', 'userLangForm', [id]), '', function (response) {
            $(profileInfoFormDiv).html(response);
        });
    };
    setUpProfileLangInfo = function (frm, gotToNextLangForm, goToTeachLang) {
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Account', 'setUpProfileLangInfo'), fcom.frmData(frm), function (t) {
            if (gotToNextLangForm && $('.profile-lang-tab.is-active').next('.profile-lang-tab').length > 0) {
                $('.profile-lang-tab.is-active').next('.profile-lang-tab').find('a').click();
            } else if (goToTeachLang) {
                $('.teacher-lang-form-js').click();
            }
        });
    };
    googleCalendarAuthorize = function () {
        fcom.updateWithAjax(fcom.makeUrl('Account', 'googleCalendarAuthorize'), '', function (response) {
            if (response.redirectUrl) {
                window.location = response.redirectUrl;
            }
        });
    };
    validateVideolink = function (field) {
        let frm = field.form;
        let url = field.value.trim();
        if (url == '') {
            return false;
        }
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
        let matches = url.match(regExp);
        if (matches && matches[2].length == 11) {
            let validUrl = "https://www.youtube.com/embed/";
            validUrl += matches[2];
            $(field).val(validUrl);
        } else {
            $(field).val('');
        }
        $(frm).validate();
    };
    formatUrl = function (fld) {
        var user_name = $(fld).val();
        user_name = user_name.trim(user_name.toLowerCase());
        user_name = user_name.replace(/[\s,<>\/\"&#%+?$@=]/g, "-");
        user_name = user_name.replace(/[\s\s]+/g, '-');
        user_name = user_name.replace(/[\-]+/g, '-');
        $(fld).val(user_name);
        $('.user_username_span').html(user_name);
        if (user_name != '') {
            checkUnique($(fld), 'tbl_users', 'user_username', 'user_id', $('#user_id'), []);
        }
    };
    toggleChangePassword = function (e, field) {
        var passType = $("input[name='" + field + "']").attr("type");
        if (passType == "text") {
            $("input[name='" + field + "']").attr("type", "password");
            $(e).html($(e).attr("data-show-caption"));
        } else {
            $("input[name='" + field + "']").attr("type", "text");
            $(e).html($(e).attr("data-hide-caption"));
        }
    };
})();
