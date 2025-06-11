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
                // If it's a PDF, display it directly
                filePreview.src = e.target.result; // Display PDF
                fileMessage.textContent = ''; // Clear any previous messages
                preview.style.display = 'block'; // Show the preview area
            } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                // For DOCX, show a message indicating conversion to PDF
                filePreview.src = ''; // Clear the iframe
                fileMessage.textContent = 'This file will be converted to PDF for preview.'; // Message for conversion
                preview.style.display = 'block'; // Show the preview area
            } else {
                filePreview.src = ''; // Clear the iframe for unsupported file types
                fileMessage.textContent = 'Unsupported file type. Please upload a PDF or DOCX file.';
                preview.style.display = 'block'; // Show the preview area
            }
        };

        if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                // Read the DOCX file as an ArrayBuffer
                                reader.readAsArrayBuffer(file);
        } else {
            // Read the file as Data URL for PDF
            reader.readAsDataURL(file);
        }
    } else {
        preview.style.display = 'none'; // Hide the preview if no file is selected
    }
});

