// File: public/js/math-editor.js
// ReadWithUs Math Editor - Uses MathLive's <math-field> custom element directly

(function(window, document) {
    'use strict';

    const RWUMath = {
        // Configuration
        config: {
            enabled: true,
            debug: true
        },

        // Log helper
        log(...args) {
            if (this.config.debug) {
                console.log('[RWUMath]', ...args);
            }
        },

        // Initialize all math fields
        init() {
            this.log('Initializing RWUMath...');
            this.log('MathLive available:', typeof MathLive !== 'undefined');
            this.log('Math-field element registered:', !!customElements?.get('math-field'));
            
            if (!this.config.enabled) {
                this.log('Math editor disabled');
                return;
            }

            // Don't check for MathLive.makeMathField - we use custom element directly
            this.initFields();
            this.setupMutationObserver();
            
            this.log('RWUMath initialized successfully');
        },

        // Initialize all math fields
        initFields() {
            const wrappers = document.querySelectorAll('[data-math-field="true"]:not([data-math-init])');
            this.log('Found', wrappers.length, 'math fields to initialize');
            
            wrappers.forEach(wrapper => {
                this.initMathField(wrapper);
            });
        },

        // Initialize a single math field
        initMathField(wrapper) {
            try {
                // Mark as initialized
                wrapper.setAttribute('data-math-init', '1');
                
                const hidden = wrapper.querySelector('input[type="hidden"]');
                const clearBtn = wrapper.querySelector('.rwu-math-clear');
                const rawPreview = wrapper.querySelector('.rwu-math-raw');
                
                if (!hidden) {
                    this.log('Missing hidden input in wrapper:', wrapper);
                    return;
                }

                // Check if math-field already exists
                let mathField = wrapper.querySelector('math-field');
                
                if (!mathField) {
                    // Create math-field element
                    mathField = document.createElement('math-field');
                    mathField.className = 'rwu-mathfield';
                    
                    // Set style
                    mathField.style.cssText = `
                        min-height: 60px;
                        width: 100%;
                        padding: 12px;
                        border: 1px solid #d1d5db;
                        border-radius: 8px;
                        background: white;
                        font-size: 18px;
                        line-height: 1.5;
                        display: block;
                    `;
                    
                    // Insert at beginning of wrapper
                    wrapper.insertBefore(mathField, wrapper.firstChild);
                }

                // Get configuration
                const layout = wrapper.getAttribute('data-keyboard') || 'basic';
                const mode = wrapper.getAttribute('data-keyboard-mode') || 'onfocus';
                
                // Set attributes for MathLive
                mathField.setAttribute('virtual-keyboard-mode', mode);
                mathField.setAttribute('virtual-keyboard-layout', layout);
                // mathField.setAttribute('virtual-keyboard-policy', 'manual');
                mathField.setAttribute('virtual-keyboard-policy', 'auto');

                
                // Set initial value
                if (hidden.value) {
                    mathField.value = hidden.value;
                }

                // Sync value to hidden input
                const syncValue = () => {
                    const latex = mathField.value || '';
                    hidden.value = latex;
                    
                    if (rawPreview) {
                        rawPreview.textContent = latex.length > 100 ? 
                            latex.substring(0, 100) + '...' : latex;
                    }
                    
                    // Dispatch change event
                    const event = new CustomEvent('math:change', {
                        bubbles: true,
                        detail: { latex, field: hidden }
                    });
                    wrapper.dispatchEvent(event);
                };

                // Event listeners
                mathField.addEventListener('input', syncValue);
                mathField.addEventListener('change', syncValue);
                
                // Focus styles
                mathField.addEventListener('focus', () => {
                    wrapper.style.borderColor = '#2DADFF';
                    wrapper.style.boxShadow = '0 0 0 3px rgba(45, 173, 255, 0.1)';
                });
                
                mathField.addEventListener('blur', () => {
                    wrapper.style.borderColor = '';
                    wrapper.style.boxShadow = '';
                });

                // Clear button
                if (clearBtn) {
                    clearBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        mathField.value = '';
                        hidden.value = '';
                        if (rawPreview) rawPreview.textContent = '';
                        mathField.focus();
                    });
                }

                // Accessibility
                mathField.setAttribute('role', 'textbox');
                mathField.setAttribute('aria-label', 'Math input field. Use on-screen keyboard for symbols.');
                mathField.setAttribute('tabindex', '0');

                // Initial sync
                syncValue();
                
                this.log('Math field initialized:', wrapper);

            } catch (error) {
                console.error('[RWUMath] Error initializing math field:', error, wrapper);
            }
        },

        // Watch for dynamically added math fields
        setupMutationObserver() {
            if (!window.MutationObserver) return;
            
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === 1) {
                                if (node.matches && node.matches('[data-math-field="true"]:not([data-math-init])')) {
                                    this.initMathField(node);
                                }
                                if (node.querySelectorAll) {
                                    node.querySelectorAll('[data-math-field="true"]:not([data-math-init])').forEach(el => {
                                        this.initMathField(el);
                                    });
                                }
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        // Public API methods
        getLatex(fieldId) {
            const wrapper = document.getElementById(fieldId);
            if (wrapper) {
                const hidden = wrapper.querySelector('input[type="hidden"]');
                return hidden ? hidden.value : '';
            }
            return '';
        },

        setLatex(fieldId, latex) {
            const wrapper = document.getElementById(fieldId);
            if (wrapper) {
                const mathField = wrapper.querySelector('math-field');
                const hidden = wrapper.querySelector('input[type="hidden"]');
                
                if (mathField) mathField.value = latex;
                if (hidden) hidden.value = latex;
            }
        },

        showKeyboard(fieldId) {
            const wrapper = document.getElementById(fieldId);
            if (wrapper) {
                const mathField = wrapper.querySelector('math-field');
                if (mathField && mathField.showVirtualKeyboard) {
                    mathField.showVirtualKeyboard();
                    mathField.focus();
                }
            }
        },

        hideKeyboard(fieldId) {
            const wrapper = document.getElementById(fieldId);
            if (wrapper) {
                const mathField = wrapper.querySelector('math-field');
                if (mathField && mathField.hideVirtualKeyboard) {
                    mathField.hideVirtualKeyboard();
                }
            }
        },

        validateForm(form) {
            let isValid = true;
            const errors = [];
            
            form.querySelectorAll('[data-math-field="true"]').forEach(wrapper => {
                const hidden = wrapper.querySelector('input[type="hidden"]');
                if (hidden && hidden.required && !hidden.value.trim()) {
                    isValid = false;
                    errors.push('Math field is required');
                    wrapper.style.borderColor = '#dc3545';
                }
            });
            
            return { isValid, errors };
        }
    };

    // Initialize when both DOM and MathLive are ready
    function initialize() {
        // Check if MathLive is loaded
        if (!window.MathLive) {
            console.warn('[RWUMath] MathLive not loaded yet. Waiting...');
            
            // Try again after a short delay
            setTimeout(initialize, 100);
            return;
        }
        
        // Check if custom element needs to be registered
        if (MathLive.registerCustomElement && !customElements.get('math-field')) {
            MathLive.registerCustomElement();
        }
        
        // Wait a moment for element registration
        setTimeout(() => {
            RWUMath.init();
        }, 100);
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        setTimeout(initialize, 100);
    }

    // Expose to global
    window.RWUMath = RWUMath;

})(window, document);