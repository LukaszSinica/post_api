import './styles/app.css';
import 'bootstrap';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

document.addEventListener('DOMContentLoaded', function() {
    const toggleHTML = function(value) {
        const quill = this.quill;
        const editor = quill.container.querySelector('.ql-editor');
        
        if (this.htmlMode) {
            // Switch back to rich text
            this.htmlMode = false;
            quill.root.innerHTML = editor.innerText;  // Convert back from HTML
            editor.setAttribute('contenteditable', 'true');
            this.container.classList.remove('active');
        } else {
            // Switch to HTML view
            this.htmlMode = true;
            const htmlContent = quill.root.innerHTML;
            editor.innerText = htmlContent;  // Show HTML as plain text
            editor.setAttribute('contenteditable', 'true');
            this.container.classList.add('active');
        }
    };

    // Add button to toolbar
    const Header = Quill.import('ui/icons');
    Header['html'] = '<svg viewBox="0 0 18 18"><text x="50%" y="50%" text-anchor="middle" dy=".3em">HTML</text></svg>';

    // Register custom handler
    Quill.register('modules/toggleHTML', function(quill, options) {
        const toolbar = quill.container.previousSibling;
        const button = toolbar.querySelector('.ql-html');
        button.addEventListener('click', toggleHTML.bind({ quill, htmlMode: false, container: button }));
    });

    const editors = document.querySelectorAll('.quill-editor');
    editors.forEach(editor => {
        // Get the associated textarea
        const textarea = editor.nextElementSibling;
        const initialContent = editor.dataset.content || '';  // Get initial content from dataset

        const quill = new Quill(editor, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        ['link', 'image'],
                        ['clean'],
                        ['html']
                    ]
                },
                toggleHTML: true
            }
        });

        // Set initial content from database
        if (initialContent) {
            quill.root.innerHTML = initialContent;
            textarea.value = initialContent;
        }

        // Update hidden form field on changes
        quill.on('text-change', () => {
            textarea.value = quill.root.innerHTML;
        });
    });

    // Initialize read-only viewer
    const viewers = document.querySelectorAll('.quill-viewer');
    viewers.forEach(viewer => {
        const quill = new Quill(viewer, {
            theme: 'snow',
            modules: {
                toolbar: false
            },
            readOnly: true
        });

        const content = viewer.dataset.content || '';
        if (content) {
            quill.root.innerHTML = content;
        }
    });

    // Image preview handler
    const imageSelect = document.querySelector('.image-select');
    const previewContainer = document.querySelector('.image-preview-container');

    if (imageSelect) {
        imageSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const imagePath = selectedOption.value ? `/uploads/images/${selectedOption.getAttribute('data-filename')}` : '';
            
            if (imagePath) {
                previewContainer.innerHTML = `<img src="${imagePath}" alt="${selectedOption.text}" class="img-preview">`;
            } else {
                previewContainer.innerHTML = '<div class="no-image">No image selected</div>';
            }
        });
    }
});