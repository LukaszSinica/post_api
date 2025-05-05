import './styles/app.css';
import 'bootstrap';
import { Modal } from 'bootstrap';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

document.addEventListener('DOMContentLoaded', function() {
    const editorElement = document.querySelector('.editor');
    if (!editorElement) return;

    // Add custom HTML button format
    const icons = Quill.import('ui/icons');
    icons['html'] = 'HTML';

    let isHtmlMode = false;
    
    const modalElement = document.getElementById('imageModal');
    const imageModal = new Modal(modalElement); 

    // Function to refresh image grid
    const refreshImageGrid = async () => {
        try {
            const response = await fetch('/editor/images/list');
            const images = await response.json();
            const imageGrid = document.getElementById('existingImages');
            imageGrid.innerHTML = '';
            
            images.forEach(image => {
                const div = document.createElement('div');
                div.className = 'image-item';
                div.innerHTML = `<img src="/uploads/images/${image}" alt="">`;
                div.onclick = () => {
                    const range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', `/uploads/images/${image}`);
                    imageModal.hide();
                };
                imageGrid.appendChild(div);
            });
        } catch (error) {
            console.error('Failed to load images:', error);
        }
    };

    // Initialize Quill
    const quill = new Quill(editorElement, {
        theme: 'snow',
        modules: {
            toolbar: {
                container: [
                    [{ 'header': [1, 2, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link', 'image'],
                    ['clean'],
                    ['html']
                ],
                handlers: {
                    html: function() {
                        
                        const htmlButton = document.querySelector('.ql-html');
                        isHtmlMode = !isHtmlMode;
                        
                        if (isHtmlMode) {
                            htmlButton.classList.add('active');
                            const htmlContent = quill.root.innerHTML;
                            const textarea = document.createElement('textarea');
                            textarea.setAttribute('id', 'html-textarea');
                            textarea.style.cssText = 'width: 100%; height: 400px; margin-top: 10px; font-family: monospace;';
                            textarea.value = htmlContent;
                            quill.root.parentNode.replaceChild(textarea, quill.root);
                        } else {
                            htmlButton.classList.remove('active');
                            const textarea = document.getElementById('html-textarea');
                            if (textarea) {
                                const htmlContent = textarea.value;
                                quill.root.innerHTML = htmlContent;
                                textarea.parentNode.replaceChild(quill.root, textarea);
                            }
                        }
                    },
                    image: function() {
                        refreshImageGrid();
                        imageModal.show();
                    }
                }
            }
        }
    });

    // Handle new image upload
    document.getElementById('imageUpload').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        const formData = new FormData();
        formData.append('imageFile', file);

        try {
            const response = await fetch('/editor/images/upload', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                // Clear the file input
                e.target.value = '';
                // Refresh the image grid
                await refreshImageGrid();
            }
        } catch (error) {
            console.error('Upload failed:', error);
        }
    });

    // Add custom button styles
    const style = document.createElement('style');
    style.innerHTML = `
        .ql-html {
            font-family: monospace;
            font-weight: bold;
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
        .ql-html.active {
            background-color: #e6e6e6;
        }
    `;
    document.head.appendChild(style);

    // Form submission handler with Symfony form field selector
    if (!quill.root.innerHTML) {
        quill.root.innerHTML = '<p></p>';
    }

    const form = document.querySelector('form');
    const contentInput = document.getElementById('posts_content');
    
    if (form && contentInput) {
        // Update content before form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            contentInput.value = quill.root.innerHTML;
            form.submit();
        });

        // Sync Quill changes to hidden input
        quill.on('text-change', function() {
            contentInput.value = quill.root.innerHTML;
        });
    }
});