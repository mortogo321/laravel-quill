/**
 * Laravel Quill - Quill Editor Integration for Laravel
 * @version 1.0.0
 * @author Mor
 * @license MIT
 */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.LaravelQuill = factory());
})(this, (function () {
    'use strict';

    class LaravelQuill {
        constructor() {
            this.editors = new Map();
            this.defaultConfig = {
                theme: 'snow',
                placeholder: 'Write something...',
            };
        }

        /**
         * Initialize all Quill editors on the page
         */
        init() {
            document.querySelectorAll('[data-quill-config]').forEach(element => {
                if (!this.editors.has(element.id)) {
                    this.initEditor(element);
                }
            });
        }

        /**
         * Initialize a single Quill editor
         * @param {HTMLElement} element - The editor container element
         * @returns {Quill} The Quill instance
         */
        initEditor(element) {
            if (typeof Quill === 'undefined') {
                console.error('Quill is not loaded. Please include Quill before laravel-quill.js');
                return null;
            }

            const config = JSON.parse(element.dataset.quillConfig || '{}');
            const inputId = element.dataset.quillInput;
            const htmlInputId = element.dataset.quillHtml;
            const uploadUrl = element.dataset.quillUploadUrl;

            // Merge with default config
            const options = { ...this.defaultConfig, ...config };

            // Create Quill instance
            const quill = new Quill(`#${element.id}`, options);

            // Store reference
            this.editors.set(element.id, quill);

            // Get input elements
            const input = document.getElementById(inputId);
            const htmlInput = document.getElementById(htmlInputId);

            // Load initial content if exists
            if (input && input.value) {
                try {
                    const delta = JSON.parse(input.value);
                    if (delta.ops) {
                        quill.setContents(delta);
                    }
                } catch (e) {
                    // If not valid JSON, treat as HTML
                    quill.clipboard.dangerouslyPasteHTML(input.value);
                }
            }

            // Update hidden inputs on content change
            quill.on('text-change', () => {
                const delta = quill.getContents();
                const html = quill.getSemanticHTML();

                if (input) {
                    input.value = JSON.stringify(delta);
                }
                if (htmlInput) {
                    htmlInput.value = html;
                }

                // Dispatch custom event
                element.dispatchEvent(new CustomEvent('quill-change', {
                    detail: { delta, html, quill },
                    bubbles: true
                }));
            });

            // Handle image upload if URL is provided
            if (uploadUrl) {
                this.setupImageUpload(quill, uploadUrl);
            }

            // Dispatch init event
            element.dispatchEvent(new CustomEvent('quill-init', {
                detail: { quill },
                bubbles: true
            }));

            return quill;
        }

        /**
         * Setup image upload handler
         * @param {Quill} quill - The Quill instance
         * @param {string} uploadUrl - The upload endpoint URL
         */
        setupImageUpload(quill, uploadUrl) {
            // Get the toolbar
            const toolbar = quill.getModule('toolbar');
            if (!toolbar) return;

            // Override the image handler
            toolbar.addHandler('image', () => {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = async () => {
                    const file = input.files[0];
                    if (!file) return;

                    // Show loading state
                    const range = quill.getSelection(true);
                    quill.insertText(range.index, 'Uploading...', { italic: true, color: '#999' });
                    quill.setSelection(range.index + 12);

                    try {
                        const url = await this.uploadImage(file, uploadUrl);

                        // Remove loading text and insert image
                        quill.deleteText(range.index, 12);
                        quill.insertEmbed(range.index, 'image', url);
                        quill.setSelection(range.index + 1);
                    } catch (error) {
                        // Remove loading text
                        quill.deleteText(range.index, 12);
                        console.error('Image upload failed:', error);
                        alert('Image upload failed: ' + error.message);
                    }
                };
            });
        }

        /**
         * Upload an image to the server
         * @param {File} file - The file to upload
         * @param {string} uploadUrl - The upload endpoint URL
         * @returns {Promise<string>} The uploaded image URL
         */
        async uploadImage(file, uploadUrl) {
            const formData = new FormData();
            formData.append('image', file);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                const error = await response.json().catch(() => ({ message: 'Upload failed' }));
                throw new Error(error.message || 'Upload failed');
            }

            const data = await response.json();
            return data.url;
        }

        /**
         * Get a Quill instance by editor ID
         * @param {string} editorId - The editor element ID
         * @returns {Quill|null} The Quill instance or null
         */
        getEditor(editorId) {
            return this.editors.get(editorId) || null;
        }

        /**
         * Get all Quill instances
         * @returns {Map} Map of editor IDs to Quill instances
         */
        getAllEditors() {
            return this.editors;
        }

        /**
         * Destroy a Quill instance
         * @param {string} editorId - The editor element ID
         */
        destroy(editorId) {
            const quill = this.editors.get(editorId);
            if (quill) {
                // Quill doesn't have a destroy method, but we can clean up our references
                this.editors.delete(editorId);
            }
        }

        /**
         * Destroy all Quill instances
         */
        destroyAll() {
            this.editors.clear();
        }

        /**
         * Get content from an editor
         * @param {string} editorId - The editor element ID
         * @returns {object|null} The Delta content or null
         */
        getContents(editorId) {
            const quill = this.getEditor(editorId);
            return quill ? quill.getContents() : null;
        }

        /**
         * Get HTML content from an editor
         * @param {string} editorId - The editor element ID
         * @returns {string|null} The HTML content or null
         */
        getHTML(editorId) {
            const quill = this.getEditor(editorId);
            return quill ? quill.getSemanticHTML() : null;
        }

        /**
         * Set content of an editor
         * @param {string} editorId - The editor element ID
         * @param {object|string} content - Delta object or HTML string
         */
        setContents(editorId, content) {
            const quill = this.getEditor(editorId);
            if (!quill) return;

            if (typeof content === 'string') {
                quill.clipboard.dangerouslyPasteHTML(content);
            } else {
                quill.setContents(content);
            }
        }

        /**
         * Enable or disable an editor
         * @param {string} editorId - The editor element ID
         * @param {boolean} enabled - Whether to enable or disable
         */
        setEnabled(editorId, enabled) {
            const quill = this.getEditor(editorId);
            if (quill) {
                quill.enable(enabled);
            }
        }

        /**
         * Focus an editor
         * @param {string} editorId - The editor element ID
         */
        focus(editorId) {
            const quill = this.getEditor(editorId);
            if (quill) {
                quill.focus();
            }
        }

        /**
         * Blur an editor
         * @param {string} editorId - The editor element ID
         */
        blur(editorId) {
            const quill = this.getEditor(editorId);
            if (quill) {
                quill.blur();
            }
        }
    }

    // Create singleton instance
    const instance = new LaravelQuill();

    // Auto-init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => instance.init());
    } else {
        instance.init();
    }

    return instance;
}));
