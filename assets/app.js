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

        // Update hidden form field
        const formField = editor.nextElementSibling;
        quill.on('text-change', () => {
            formField.value = quill.root.innerHTML;
        });
    });
});