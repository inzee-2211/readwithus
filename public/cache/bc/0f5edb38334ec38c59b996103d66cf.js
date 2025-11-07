/*
* jQuery UI Tag-it!
*
* @version v2.0 (06/2011)
*
* Copyright 2011, Levy Carneiro Jr.
* Released under the MIT license.
* http://aehlke.github.com/tag-it/LICENSE
*
* Homepage:
*   http://aehlke.github.com/tag-it/
*
* Authors:
*   Levy Carneiro Jr.
*   Martin Rehfeld
*   Tobias Schmidt
*   Skylar Challand
*   Alex Ehlke
*
* Maintainer:
*   Alex Ehlke - Twitter: @aehlke
*
* Dependencies:
*   jQuery v1.4+
*   jQuery UI v1.8+
*/
(function($) {

    $.widget('ui.tagit', {
        options: {
            allowDuplicates   : false,
            caseSensitive     : true,
            fieldName         : 'tags',
            placeholderText   : null,   // Sets `placeholder` attr on input field.
            readOnly          : false,  // Disables editing.
            removeConfirmation: false,  // Require confirmation to remove tags.
            tagLimit          : null,   // Max number of tags allowed (null for unlimited).

            // Used for autocomplete, unless you override `autocomplete.source`.
            availableTags     : [],

            // Use to override or add any options to the autocomplete widget.
            //
            // By default, autocomplete.source will map to availableTags,
            // unless overridden.
            autocomplete: {},

            // Shows autocomplete before the user even types anything.
            showAutocompleteOnFocus: false,

            // When enabled, quotes are unneccesary for inputting multi-word tags.
            allowSpaces: false,

            // The below options are for using a single field instead of several
            // for our form values.
            //
            // When enabled, will use a single hidden field for the form,
            // rather than one per tag. It will delimit tags in the field
            // with singleFieldDelimiter.
            //
            // The easiest way to use singleField is to just instantiate tag-it
            // on an INPUT element, in which case singleField is automatically
            // set to true, and singleFieldNode is set to that element. This
            // way, you don't need to fiddle with these options.
            singleField: false,

            // This is just used when preloading data from the field, and for
            // populating the field with delimited tags as the user adds them.
            singleFieldDelimiter: ',',

            // Set this to an input DOM node to use an existing form field.
            // Any text in it will be erased on init. But it will be
            // populated with the text of tags as they are created,
            // delimited by singleFieldDelimiter.
            //
            // If this is not set, we create an input node for it,
            // with the name given in settings.fieldName.
            singleFieldNode: null,

            // Whether to animate tag removals or not.
            animate: true,

            // Optionally set a tabindex attribute on the input that gets
            // created for tag-it.
            tabIndex: null,

            // Event callbacks.
            beforeTagAdded      : null,
            afterTagAdded       : null,

            beforeTagRemoved    : null,
            afterTagRemoved     : null,

            onTagClicked        : null,
            onTagLimitExceeded  : null,


            // DEPRECATED:
            //
            // /!\ These event callbacks are deprecated and WILL BE REMOVED at some
            // point in the future. They're here for backwards-compatibility.
            // Use the above before/after event callbacks instead.
            onTagAdded  : null,
            onTagRemoved: null,
            // `autocomplete.source` is the replacement for tagSource.
            tagSource: null
            // Do not use the above deprecated options.
        },

        _create: function() {
            // for handling static scoping inside callbacks
            var that = this;

            // There are 2 kinds of DOM nodes this widget can be instantiated on:
            //     1. UL, OL, or some element containing either of these.
            //     2. INPUT, in which case 'singleField' is overridden to true,
            //        a UL is created and the INPUT is hidden.
            if (this.element.is('input')) {
                this.tagList = $('<ul></ul>').insertAfter(this.element);
                this.options.singleField = true;
                this.options.singleFieldNode = this.element;
                this.element.addClass('tagit-hidden-field');
            } else {
                this.tagList = this.element.find('ul, ol').andSelf().last();
            }

            this.tagInput = $('<input type="text" />').addClass('ui-widget-content');

            if (this.options.readOnly) this.tagInput.attr('disabled', 'disabled');

            if (this.options.tabIndex) {
                this.tagInput.attr('tabindex', this.options.tabIndex);
            }

            if (this.options.placeholderText) {
                this.tagInput.attr('placeholder', this.options.placeholderText);
            }

            if (!this.options.autocomplete.source) {
                this.options.autocomplete.source = function(search, showChoices) {
                    var filter = search.term.toLowerCase();
                    var choices = $.grep(this.options.availableTags, function(element) {
                        // Only match autocomplete options that begin with the search term.
                        // (Case insensitive.)
                        return (element.toLowerCase().indexOf(filter) === 0);
                    });
                    if (!this.options.allowDuplicates) {
                        choices = this._subtractArray(choices, this.assignedTags());
                    }
                    showChoices(choices);
                };
            }

            if (this.options.showAutocompleteOnFocus) {
                this.tagInput.focus(function(event, ui) {
                    that._showAutocomplete();
                });

                if (typeof this.options.autocomplete.minLength === 'undefined') {
                    this.options.autocomplete.minLength = 0;
                }
            }

            // Bind autocomplete.source callback functions to this context.
            if ($.isFunction(this.options.autocomplete.source)) {
                this.options.autocomplete.source = $.proxy(this.options.autocomplete.source, this);
            }

            // DEPRECATED.
            if ($.isFunction(this.options.tagSource)) {
                this.options.tagSource = $.proxy(this.options.tagSource, this);
            }

            this.tagList
                .addClass('tagit')
                .addClass('ui-widget ui-widget-content ui-corner-all')
                // Create the input field.
                .append($('<li class="tagit-new"></li>').append(this.tagInput))
                .click(function(e) {
                    var target = $(e.target);
                    if (target.hasClass('tagit-label')) {
                        var tag = target.closest('.tagit-choice');
                        if (!tag.hasClass('removed')) {
                            that._trigger('onTagClicked', e, {tag: tag, tagLabel: that.tagLabel(tag)});
                        }
                    } else {
                        // Sets the focus() to the input field, if the user
                        // clicks anywhere inside the UL. This is needed
                        // because the input field needs to be of a small size.
                        that.tagInput.focus();
                    }
                });

            // Single field support.
            var addedExistingFromSingleFieldNode = false;
            if (this.options.singleField) {
                if (this.options.singleFieldNode) {
                    // Add existing tags from the input field.
                    var node = $(this.options.singleFieldNode);
                    var tags = node.val().split(this.options.singleFieldDelimiter);
                    node.val('');
                    $.each(tags, function(index, tag) {
                        that.createTag(tag, null, true);
                        addedExistingFromSingleFieldNode = true;
                    });
                } else {
                    // Create our single field input after our list.
                    this.options.singleFieldNode = $('<input type="hidden" style="display:none;" value="" name="' + this.options.fieldName + '" />');
                    this.tagList.after(this.options.singleFieldNode);
                }
            }

            // Add existing tags from the list, if any.
            if (!addedExistingFromSingleFieldNode) {
                this.tagList.children('li').each(function() {
                    if (!$(this).hasClass('tagit-new')) {
                        that.createTag($(this).text(), $(this).attr('class'), true);
                        $(this).remove();
                    }
                });
            }

            // Events.
            this.tagInput
                .keydown(function(event) {
                    // Backspace is not detected within a keypress, so it must use keydown.
                    if (event.which == $.ui.keyCode.BACKSPACE && that.tagInput.val() === '') {
                        var tag = that._lastTag();
                        if (!that.options.removeConfirmation || tag.hasClass('remove')) {
                            // When backspace is pressed, the last tag is deleted.
                            that.removeTag(tag);
                        } else if (that.options.removeConfirmation) {
                            tag.addClass('remove ui-state-highlight');
                        }
                    } else if (that.options.removeConfirmation) {
                        that._lastTag().removeClass('remove ui-state-highlight');
                    }

                    // Comma/Space/Enter are all valid delimiters for new tags,
                    // except when there is an open quote or if setting allowSpaces = true.
                    // Tab will also create a tag, unless the tag input is empty,
                    // in which case it isn't caught.
                    if (
                        (event.which === $.ui.keyCode.COMMA && event.shiftKey === false) ||
                        event.which === $.ui.keyCode.ENTER ||
                        (
                            event.which == $.ui.keyCode.TAB &&
                            that.tagInput.val() !== ''
                        ) ||
                        (
                            event.which == $.ui.keyCode.SPACE &&
                            that.options.allowSpaces !== true &&
                            (
                                $.trim(that.tagInput.val()).replace( /^s*/, '' ).charAt(0) != '"' ||
                                (
                                    $.trim(that.tagInput.val()).charAt(0) == '"' &&
                                    $.trim(that.tagInput.val()).charAt($.trim(that.tagInput.val()).length - 1) == '"' &&
                                    $.trim(that.tagInput.val()).length - 1 !== 0
                                )
                            )
                        )
                    ) {
                        // Enter submits the form if there's no text in the input.
                        if (!(event.which === $.ui.keyCode.ENTER && that.tagInput.val() === '')) {
                            event.preventDefault();
                        }

                        // Autocomplete will create its own tag from a selection and close automatically.
                        if (!(that.options.autocomplete.autoFocus && that.tagInput.data('autocomplete-open'))) {
                            that.tagInput.autocomplete('close');
                            that.createTag(that._cleanedInput());
                        }
                    }
                }).blur(function(e){
                    // Create a tag when the element loses focus.
                    // If autocomplete is enabled and suggestion was clicked, don't add it.
                    if (!that.tagInput.data('autocomplete-open')) {
                        that.createTag(that._cleanedInput());
                    }
                });

            // Autocomplete.
            if (this.options.availableTags || this.options.tagSource || this.options.autocomplete.source) {
                var autocompleteOptions = {
                    select: function(event, ui) {
                        that.createTag(ui.item.value);
                        // Preventing the tag input to be updated with the chosen value.
                        return false;
                    }
                };
                $.extend(autocompleteOptions, this.options.autocomplete);

                // tagSource is deprecated, but takes precedence here since autocomplete.source is set by default,
                // while tagSource is left null by default.
                autocompleteOptions.source = this.options.tagSource || autocompleteOptions.source;

                this.tagInput.autocomplete(autocompleteOptions).bind('autocompleteopen.tagit', function(event, ui) {
                    that.tagInput.data('autocomplete-open', true);
                }).bind('autocompleteclose.tagit', function(event, ui) {
                    that.tagInput.data('autocomplete-open', false)
                });

                this.tagInput.autocomplete('widget').addClass('tagit-autocomplete');
            }
        },

        destroy: function() {
            $.Widget.prototype.destroy.call(this);

            this.element.unbind('.tagit');
            this.tagList.unbind('.tagit');

            this.tagInput.removeData('autocomplete-open');

            this.tagList.removeClass([
                'tagit',
                'ui-widget',
                'ui-widget-content',
                'ui-corner-all',
                'tagit-hidden-field'
            ].join(' '));

            if (this.element.is('input')) {
                this.element.removeClass('tagit-hidden-field');
                this.tagList.remove();
            } else {
                this.element.children('li').each(function() {
                    if ($(this).hasClass('tagit-new')) {
                        $(this).remove();
                    } else {
                        $(this).removeClass([
                            'tagit-choice',
                            'ui-widget-content',
                            'ui-state-default',
                            'ui-state-highlight',
                            'ui-corner-all',
                            'remove',
                            'tagit-choice-editable',
                            'tagit-choice-read-only'
                        ].join(' '));

                        $(this).text($(this).children('.tagit-label').text());
                    }
                });

                if (this.singleFieldNode) {
                    this.singleFieldNode.remove();
                }
            }

            return this;
        },

        _cleanedInput: function() {
            // Returns the contents of the tag input, cleaned and ready to be passed to createTag
            return $.trim(this.tagInput.val().replace(/^"(.*)"$/, '$1'));
        },

        _lastTag: function() {
            return this.tagList.find('.tagit-choice:last:not(.removed)');
        },

        _tags: function() {
            return this.tagList.find('.tagit-choice:not(.removed)');
        },

        assignedTags: function() {
            // Returns an array of tag string values
            var that = this;
            var tags = [];
            if (this.options.singleField) {
                tags = $(this.options.singleFieldNode).val().split(this.options.singleFieldDelimiter);
                if (tags[0] === '') {
                    tags = [];
                }
            } else {
                this._tags().each(function() {
                    tags.push(that.tagLabel(this));
                });
            }
            return tags;
        },

        _updateSingleTagsField: function(tags) {
            // Takes a list of tag string values, updates this.options.singleFieldNode.val to the tags delimited by this.options.singleFieldDelimiter
            $(this.options.singleFieldNode).val(tags.join(this.options.singleFieldDelimiter)).trigger('change');
        },

        _subtractArray: function(a1, a2) {
            var result = [];
            for (var i = 0; i < a1.length; i++) {
                if ($.inArray(a1[i], a2) == -1) {
                    result.push(a1[i]);
                }
            }
            return result;
        },

        tagLabel: function(tag) {
            // Returns the tag's string label.
            if (this.options.singleField) {
                return $(tag).find('.tagit-label:first').text();
            } else {
                return $(tag).find('input:first').val();
            }
        },

        _showAutocomplete: function() {
            this.tagInput.autocomplete('search', '');
        },

        _findTagByLabel: function(name) {
            var that = this;
            var tag = null;
            this._tags().each(function(i) {
                if (that._formatStr(name) == that._formatStr(that.tagLabel(this))) {
                    tag = $(this);
                    return false;
                }
            });
            return tag;
        },

        _isNew: function(name) {
            return !this._findTagByLabel(name);
        },

        _formatStr: function(str) {
            if (this.options.caseSensitive) {
                return str;
            }
            return $.trim(str.toLowerCase());
        },

        _effectExists: function(name) {
            return Boolean($.effects && ($.effects[name] || ($.effects.effect && $.effects.effect[name])));
        },

        createTag: function(value, additionalClass, duringInitialization) {
            var that = this;

            value = $.trim(value);

            if(this.options.preprocessTag) {
                value = this.options.preprocessTag(value);
            }

            if (value === '') {
                return false;
            }

            if (!this.options.allowDuplicates && !this._isNew(value)) {
                var existingTag = this._findTagByLabel(value);
                if (this._trigger('onTagExists', null, {
                    existingTag: existingTag,
                    duringInitialization: duringInitialization
                }) !== false) {
                    if (this._effectExists('highlight')) {
                        existingTag.effect('highlight');
                    }
                }
                return false;
            }

            if (this.options.tagLimit && this._tags().length >= this.options.tagLimit) {
                this._trigger('onTagLimitExceeded', null, {duringInitialization: duringInitialization});
                return false;
            }

            var label = $(this.options.onTagClicked ? '<a class="tagit-label"></a>' : '<span class="tagit-label"></span>').text(value);

            // Create tag.
            var tag = $('<li></li>')
                .addClass('tagit-choice ui-widget-content ui-state-default ui-corner-all')
                .addClass(additionalClass)
                .append(label);

            if (this.options.readOnly){
                tag.addClass('tagit-choice-read-only');
            } else {
                tag.addClass('tagit-choice-editable');
                // Button for removing the tag.
                var removeTagIcon = $('<span></span>')
                    .addClass('ui-icon ui-icon-close');
                var removeTag = $('<a><span class="text-icon">\xd7</span></a>') // \xd7 is an X
                    .addClass('tagit-close')
                    .append(removeTagIcon)
                    .click(function(e) {
                        // Removes a tag when the little 'x' is clicked.
                        that.removeTag(tag);
                    });
                tag.append(removeTag);
            }

            // Unless options.singleField is set, each tag has a hidden input field inline.
            if (!this.options.singleField) {
                var escapedValue = label.html();
                tag.append('<input type="hidden" value="' + escapedValue + '" name="' + this.options.fieldName + '" class="tagit-hidden-field" />');
            }

            if (this._trigger('beforeTagAdded', null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            }) === false) {
                return;
            }

            if (this.options.singleField) {
                var tags = this.assignedTags();
                tags.push(value);
                this._updateSingleTagsField(tags);
            }

            // DEPRECATED.
            this._trigger('onTagAdded', null, tag);

            this.tagInput.val('');

            // Insert tag.
            this.tagInput.parent().before(tag);

            this._trigger('afterTagAdded', null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            });

            if (this.options.showAutocompleteOnFocus && !duringInitialization) {
                setTimeout(function () { that._showAutocomplete(); }, 0);
            }
        },

        removeTag: function(tag, animate) {
            animate = typeof animate === 'undefined' ? this.options.animate : animate;

            tag = $(tag);

            // DEPRECATED.
            this._trigger('onTagRemoved', null, tag);

            if (this._trigger('beforeTagRemoved', null, {tag: tag, tagLabel: this.tagLabel(tag)}) === false) {
                return;
            }

            if (this.options.singleField) {
                var tags = this.assignedTags();
                var removedTagLabel = this.tagLabel(tag);
                tags = $.grep(tags, function(el){
                    return el != removedTagLabel;
                });
                this._updateSingleTagsField(tags);
            }

            if (animate) {
                tag.addClass('removed'); // Excludes this tag from _tags.
                var hide_args = this._effectExists('blind') ? ['blind', {direction: 'horizontal'}, 'fast'] : ['fast'];

                var thisTag = this;
                hide_args.push(function() {
                    tag.remove();
                    thisTag._trigger('afterTagRemoved', null, {tag: tag, tagLabel: thisTag.tagLabel(tag)});
                });

                tag.fadeOut('fast').hide.apply(tag, hide_args).dequeue();
            } else {
                tag.remove();
                this._trigger('afterTagRemoved', null, {tag: tag, tagLabel: this.tagLabel(tag)});
            }

        },

        removeTagByLabel: function(tagLabel, animate) {
            var toRemove = this._findTagByLabel(tagLabel);
            if (!toRemove) {
                throw "No such tag exists with the name '" + tagLabel + "'";
            }
            this.removeTag(toRemove, animate);
        },

        removeAll: function() {
            // Removes all tags.
            var that = this;
            this._tags().each(function(index, tag) {
                that.removeTag(tag, false);
            });
        }

    });
})(jQuery);

/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);var lectureId;
$(function () {
    generalForm = function () {
        fcom.ajax(fcom.makeUrl('Courses', 'generalForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            var id = $('textarea[name="course_details"]').attr('id');
            window["oEdit_" + id].disableFocusOnLoad = true;
            $('#pageContentJs input[name="course_title"]:first').focus();
            getCourseEligibility();
            fcom.setEditorLayout(siteLangId);
        });
    };
    
    mediaForm = function (process = true) {
        fcom.ajax(fcom.makeUrl('Courses', 'mediaForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            getCourseEligibility();
        }, {process: process});
    };
    intendedLearnersForm = function () {
        fcom.ajax(fcom.makeUrl('Courses', 'intendedLearnersForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            getCourseEligibility();
        });
    };
    addFld = function (type) {
        $('.typesAreaJs' + type + " .typesListJs").append($('.typesAreaJs' + type + " .typeFieldsJs:last").clone().find("input:text, input:hidden").val("").end());
        var obj = $('.typesAreaJs' + type + " .typeFieldsJs:last");
        $(obj).find("a.sortHandlerJs").removeClass('sortHandlerJs');
        $(obj).find("a.removeRespJs").attr('onclick', "removeIntendedLearner(this, 0);").show();
        $(obj).find(".field-count").attr('field-count', $(obj).find(".field-count").data('length'));
    };
    setupIntendedLearners = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Courses', 'setupIntendedLearners'), data, function (res) {
            priceForm();
            getCourseEligibility();
        });
    };
    updateIntendedOrder = function() {
        var order = [];
        $('.sortable_ids').each(function () {
            order.push($(this).val());
        });
        fcom.ajax(fcom.makeUrl('Courses', 'updateIntendedOrder'), {
            'order': order
        }, function (res) {
            intendedLearnersForm();
        });
    }
    removeIntendedLearner = function (obj, id) {
        if (id > 0) {
            if (confirm(langLbl.confirmRemove)) {
                fcom.updateWithAjax(fcom.makeUrl('Courses', 'deleteIntendedLearner', [id]), '', function (res) {
                    $(obj).parents('.typeFieldsJs').remove();
                });
            }
            getCourseEligibility();
        } else {
            $(obj).parents('.typeFieldsJs').remove();
        }
    };
    priceForm = function () {
        fcom.ajax(fcom.makeUrl('Courses', 'priceForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            updatePriceForm($('input[name="course_type"]:checked').val());
            getCourseEligibility();
        });
    };
    updatePriceForm = function (type) {
        if (type == TYPE_FREE) {
            $('select[name="course_currency_id"], input[name="course_price"]').attr("data-fatreq", '{"required":false}').val('');
            $('.reqFldsJs').hide();

        } else {
            $('select[name="course_currency_id"], input[name="course_price"]').attr("data-fatreq", '{"required":true}');
            $('.reqFldsJs').show();
        }
    };
    setupPrice = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Courses', 'setupPrice'), data, function (res) {
            curriculumForm();
            getCourseEligibility();
        });
    };
    curriculumForm = function () {
        fcom.ajax(fcom.makeUrl('Courses', 'curriculumForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            searchSections();
            getCourseEligibility();
        });
    };
    settingsForm = function () {
        fcom.ajax(fcom.makeUrl('Courses', 'settingsForm', [courseId]), '', function (res) {
            $('#pageContentJs').html(res);
            getCourseEligibility();
        });
    };
    setupSettings = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Courses', 'setupSettings'), data, function (res) {
            settingsForm();
        });
    };
    setup = function () {
        var frm = $('#frmCourses');
        if (!$(frm).validate()) {
            return;
        }

        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Courses', 'setup'), data, function (res) {
            $('#mainHeadingJs').text(res.title);
            window.history.pushState('page', document.title, fcom.makeUrl('Courses', 'form', [res.courseId]));
            courseId = res.courseId;
            mediaForm();
            getCourseEligibility();
        });
    };
    setupMedia = function () {
        var frm = $('#frmCourses')[0];
        var data = new FormData(frm);
        frm.reset();
        fcom.ajaxMultipart(fcom.makeUrl('Courses', 'setupMedia'), data, function (res) {
            if (res.status == 1) {
                mediaForm(false);
                getCourseEligibility();
            }
        }, { fOutMode: 'json' });
    };
    removeMedia = function(type) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(fcom.makeUrl('Courses', 'removeMedia', [courseId]), {type}, function (res) {
                mediaForm();
                getCourseEligibility();
            });
        }
    };
    generalForm();
    getCourseEligibility = function () {
        fcom.updateWithAjax(fcom.makeUrl('Courses', 'getEligibilityStatus', [courseId]), '', function (res) {
            setCompletedStatus(res.criteria);
        }, {'process' : false});
    };
    setCompletedStatus = function (criteria) {
        $('.general-info-js, .intended-learner-js, .course-price-js, .curriculum-js, .course-setting-js').removeClass('is-completed').addClass('is-progress');
        $('.btnApprovalJs').addClass('d-none');
        if (criteria.course_lang == 1 && criteria.course_image == 1 && criteria.course_preview_video == 1 && criteria.course_cate == 1 && criteria.course_subcate == 1 && criteria.course_clang == 1) {
            $('.general-info-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.courses_intended_learners == 1) {
            $('.intended-learner-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_price == 1 && criteria.course_currency_id == 1) {
            $('.course-price-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_sections == 1 && criteria.course_lectures == 1) {
            $('.curriculum-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_tags == 1) {
            $('.course-setting-js').removeClass('is-progress').addClass('is-completed');
        }
        if (criteria.course_is_eligible == true) {
            $('.btnApprovalJs').removeClass('d-none');
        }
    };
    getCourseEligibility();

    /* Sections [ */
    sectionForm = function (id) {
        var section_order = $('#courseSectionOrderJs').val();
        fcom.ajax(fcom.makeUrl('Sections', 'form', [courseId]), { section_order, id }, function (res) {
            $('.message-display').remove();
            if (id > 0) {
                $('#sectionId' + id + " .sectionCardJs").hide();
                $('#sectionId' + id + " .sectionEditCardJs").html(res);
            } else {
                $('#courseSectionOrderJs').val(parseInt(section_order) + 1);
                $('#sectionFormAreaJs').append(res);
                $('body, html').animate({ scrollTop: $("#sectionForm" + section_order + '1').offset().top }, 1000);
            }
        });
    };
    setupSection = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Sections', 'setup'), data, function (res) {
            $(frm).parents('.card-panel').remove();
            searchSections();
            getCourseEligibility();
        });
    };
    updateSectionOrder = function () {
        var order = [''];
        $('#sectionAreaJs .card-panel').each(function () {
            order.push($(this).data('id'));
        });
        fcom.ajax(fcom.makeUrl('Sections', 'updateOrder', [courseId]), {
            'order': order
        }, function (res) {
            searchSections();
        });
    };
    removeSection = function (id) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('Sections', 'delete', [id]), '', function (res) {
                searchSections();
                getCourseEligibility();
            });
        }
    };
    cancelSection = function (id) {
        $("#sectionForm" + id).remove();
        if ($('#sectionAreaJs .card-panel').length < 1) {
            searchSections();
        }
    };
    searchSections = function (sectionId) {
        fcom.ajax(fcom.makeUrl('Sections', 'search', [courseId]), { 'section_id': sectionId }, function (res) {
            if (sectionId > 0) {
                $('#sectionId' + sectionId).html(res);
                return;
            } else {
                $('#sectionAreaJs').html(res);
            }
        });
    };
    /* ] */

    /* Lectures [ */
    lectureForm = function (sectionId, lectureId = 0) {
        var lectureOrder = $('#lectureOrderJs').val();
        fcom.ajax(fcom.makeUrl('Lectures', 'form', [sectionId]), { 'lecture_id': lectureId, 'lecture_order': lectureOrder, 'course_id': courseId }, function (res) {
            /* for edit, append form to the current lecture area */
            if ($('#sectionLectures' + lectureId).length > 0) {
                $('#sectionLectures' + lectureId).replaceWith(res).show();
            } else {
                /* if new form added, append it to the last */
                $('#sectionId' + sectionId + ' .lecturesListJs').append(res).show();
                $('#lectureOrderJs').val(parseInt(lectureOrder) + 1);
            }
            var id = $(res).find('textarea[name="lecture_details"]').attr('id');
            window["oEdit_" + id].disableFocusOnLoad = true;
            fcom.setEditorLayout(siteLangId);
        });
    };
    $(document).on('submit', 'form[name=frmLecture]', function (event) {
        var frm = $(this);
        if (!$(frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Lectures', 'setup'), fcom.frmData(frm), function (res) {
            $(frm).parents('.card-group-js').attr('id', 'sectionLectures' + res.lectureId);
            lectureMediaForm(res.lectureId);
            getCourseEligibility();
        });
        return false;
    });
    updateLectureOrder = function () {
        var order = [''];
        $('#sectionAreaJs .lecturePanelJs').each(function () {
            order.push($(this).data('id'));
        });
        fcom.ajax(fcom.makeUrl('Lectures', 'updateOrder'), {
            'order': order
        }, function (res) {
            searchSections();
        });
    };
    removeLecture = function (sectionId, id) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('Lectures', 'delete', [id]), '', function (res) {
                searchSections(sectionId);
                getCourseEligibility();
            });
        }
    };
    cancelLecture = function (lectureId) {
        fcom.ajax(fcom.makeUrl('Lectures', 'search', [lectureId]), '', function (res) {
            var ele = $('#sectionLectures' + lectureId);
            $('#sectionLectures' + lectureId).before(res);
            $(ele).remove();
        });
    };
    removeLectureForm = function (sectionId, id) {
        $(id).remove();
        if ($('#sectionId' + sectionId + ' .lecturesListJs .card-group-js').length == 0) {
            $('#sectionId' + sectionId + ' .lecturesListJs').hide();
        }
    };
    lectureMediaForm = function (lectureId) {
        fcom.ajax(fcom.makeUrl('Lectures', 'mediaForm', [lectureId]), '', function (res) {
            $('#sectionLectures' + lectureId).html(res);
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
    setupLectureMedia = function (frm) {
        if (!$("#" + frm).validate()) {
            return;
        }
        fcom.updateWithAjax(fcom.makeUrl('Lectures', 'setupMedia'), fcom.frmData($("#" + frm)), function (res) {
            lectureResourceForm(res.lectureId);
        });
    };
    lectureResourceForm = function (lectureId, process = true) {
        fcom.ajax(fcom.makeUrl('LectureResources', 'index', [lectureId]), '', function (res) {
            $('#sectionLectures' + lectureId).html(res);
        }, { process: process });
    };
    setupLectureResrc = function (frm) {
        if (!$(frm).validate()) {
            return;
        }
        var data = new FormData(frm);
        fcom.ajaxMultipart(fcom.makeUrl('LectureResources', 'setup'), data, function (res) {
            lectureResourceForm(res.lectureId, false);
            $.facebox.close();
        }, { fOutMode: 'json' });
    };
    uploadResource = function (id) {
        var frm = $('#' + id)[0];
        setupLectureResrc(frm);
    };
    removeLectureResrc = function (id, lectureId) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.ajax(fcom.makeUrl('LectureResources', 'delete', [id]), '', function (res) {
                lectureResourceForm(lectureId);
            });
        }
    };
    getResources = function (lecId) {
        fcom.ajax(fcom.makeUrl('LectureResources', 'resources', [lecId]), '', function (res) {
            $.facebox(res, 'facebox-medium padding-0');
            lectureId = lecId;
            searchResources(document.frmResourceSearch);
        });
    };
    searchResources = function (frm, page = 1) {
        document.frmResourceSearch.page.value = page;
        fcom.updateWithAjax(fcom.makeUrl('LectureResources', 'search', [lectureId]), fcom.frmData(frm), function (res) {
            if (page > 1) {
                $('#listingJs').append(res.html);
            } else {
                $('#listingJs').html(res.html);
            }
            if (res.loadMore == 1) {
                $('.rvwLoadMoreJs a').data('page', res.nextPage);
                $('.rvwLoadMoreJs').show();
            } else {
                $('.rvwLoadMoreJs').hide();
            }
        });
    };
    resourcePaging = function (_obj) {
        searchResources(document.frmResourceSearch, $(_obj).data('page'));
    };
    /* ] */
    submitForReview = function () {
        if (confirm(langLbl.confirmCourseSubmission)) {
            fcom.updateWithAjax(fcom.makeUrl('Courses', 'submitForApproval', [courseId]), '', function (res) {
                window.location = fcom.makeUrl('Courses');
            });
        }
    }
});
$(document).ready(function(){
    $('body').on('input', 'input[type="text"], textarea', function () {
        var ele = $(this).parent();
        if ($(ele).hasClass('field-count')) {
            var max = parseInt($(ele).data('length'));
            var strLen = parseInt($(this).val().length);
            var limit = max - strLen;
            if (limit < 0) {
                $(this).val($(this).val().substring(0, max));
                $(ele).attr('field-count', 0);
                return;
            }
            $(ele).attr('field-count', limit);
        }
    });
});
getSubCategories = function (id, selectedId = 0) {
    fcom.ajax(fcom.makeUrl('Courses', 'getSubcategories', [id, selectedId]), '', function (res) {
        $("#subCategories").html(res);
    });
};