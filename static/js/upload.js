document.getElementById('resume').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const filePreview = document.getElementById('file-preview');
    const fileMessage = document.getElementById('file-message');

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const fileType = file.type;
            if (fileType === 'application/pdf') {
                filePreview.src = e.target.result; 
                fileMessage.textContent = '';
                preview.style.display = 'block'; 
            } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                
                filePreview.src = ''; 
                fileMessage.textContent = 'This file will be converted to PDF for preview.'; 
                preview.style.display = 'block'; 
            } else {
                filePreview.src = ''; 
                fileMessage.textContent = 'Unsupported file type. Please upload a PDF or DOCX file.';
                preview.style.display = 'block'; 
            }
        };

        if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                
                                reader.readAsArrayBuffer(file);
        } else {
            reader.readAsDataURL(file);
        }
    } else {
        preview.style.display = 'none'; 
    }
});

