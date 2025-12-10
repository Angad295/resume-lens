# AI Powered Resume Analyser

An intelligent web application that analyzes resumes using AI technology to provide actionable feedback on industry readiness, strengths, and areas for improvement.

## 🌟 Features

- **AI-Powered Analysis**: Utilizes Google's Gemini 2.5 Flash API to provide detailed resume feedback
- **Multiple File Format Support**: Accepts PDF, DOC, and DOCX file formats
- **Smart Text Extraction**: 
  - PDF parsing using smalot/pdfparser
  - DOCX parsing using PhpOffice/PhpWord
- **Comprehensive Feedback**: Provides:
  - Overall score out of 10
  - Strengths summary
  - Bullet-pointed improvement suggestions
  - Industry readiness recommendations
- **Dark/Light Theme Toggle**: User-customizable interface with theme persistence
- **Sample Resume Templates**: Access to exemplary resume templates for reference
- **Feedback System**: Built-in suggestion and feature request form

## 🚀 Live Demo

Upload your resume and receive instant AI-powered analysis to improve your job application materials.

## 📋 Prerequisites

- PHP 7.4 or higher
- Composer (PHP dependency manager)
- Web server (Apache/Nginx)
- Google Gemini API key

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd resume-analyzer
   ```

2. **Install PHP dependencies**
   ```bash
   composer require smalot/pdfparser
   composer require phpoffice/phpword
   ```

3. **Configure API Key**
   
   Open `analyze.php` and replace the API key:
   ```php
   $GEMINI_API_KEY = getenv('GEMINI_API_KEY') ?: 'YOUR_API_KEY_HERE';
   ```
   
   **Recommended**: Set as environment variable for security:
   ```bash
   export GEMINI_API_KEY='your-actual-api-key'
   ```

4. **Set up file permissions**
   ```bash
   mkdir uploads
   chmod 755 uploads
   ```

5. **Deploy to web server**
   - Point your web server document root to the project directory
   - Ensure PHP is properly configured

## 📁 Project Structure

```
resume-analyzer/
│
├── index.html              # Main upload page
├── analyze.php             # Backend processing and AI analysis
├── sample.html             # Resume template showcase
├── suggestions.html        # Feedback submission form
├── style.css               # Application styling with theme support
├── script.js               # Client-side interactivity
├── uploads/                # Temporary file storage (auto-created)
├── vendor/                 # Composer dependencies
├── composer.json           # PHP dependencies configuration
└── README.md               # This file
```

## 💻 Usage

1. **Navigate to the application** in your web browser
2. **Upload your resume** (PDF, DOC, or DOCX format, max 5MB)
3. **Receive instant AI feedback** including:
   - Overall resume score
   - Key strengths
   - Improvement recommendations
   - ATS compatibility suggestions
4. **Review sample templates** for inspiration
5. **Toggle dark/light mode** for comfortable viewing

## 🔒 Security Features

- File type validation (only PDF, DOC, DOCX allowed)
- File size limits (5MB maximum)
- Uploaded files are automatically deleted after processing
- HTML output sanitization to prevent XSS attacks
- Unique file naming to prevent overwrites

## 🎨 Customization

### Theme Toggle
The application includes a dark/light mode toggle with localStorage persistence. Users' theme preferences are saved between sessions.

### AI Prompt Customization
Modify the AI analysis prompt in `analyze.php`:
```php
$prompt = "Your custom analysis instructions here...";
```

## 📊 Sample Templates

The application includes links to professional resume samples:
- Functional Resume
- Creative Resume
- Minimalist Resume

## 🤝 Contributing

Have ideas for improvement? Use the built-in suggestion form at `/suggestions.html` or submit a pull request.

## ⚠️ Important Notes

- **Legacy .doc files** are not supported for text extraction. Users should convert to DOCX or PDF format.
- **API costs**: The Google Gemini API may have usage limits or costs associated with it.
- **Demo mode**: If no valid API key is configured, the application will return demo analysis results.
- **Privacy**: Uploaded files are temporarily stored and deleted after processing. No data is permanently retained.

## 🐛 Troubleshooting

### Text extraction fails
- Ensure the PDF is not scanned/image-based
- Try converting DOC files to DOCX format
- Check file permissions on the uploads directory

### API errors
- Verify your Gemini API key is valid and active
- Check API quota limits
- Review error messages in PHP error logs

### Upload issues
- Confirm file size is under 5MB
- Verify file extension is .pdf, .doc, or .docx
- Check server upload_max_filesize in php.ini

## 📝 License

This project is provided as-is for educational and professional development purposes.

## 👨‍💻 Author

Built with ❤️ by Angad Pal Singh

## 🙏 Acknowledgments

- Google Gemini API for AI analysis capabilities
- smalot/pdfparser for PDF text extraction
- PhpOffice/PhpWord for DOCX processing
- Sample resume templates from Colorado State University and Seton Hall University

---

**Disclaimer**: This tool provides suggestions for informational purposes only and should not be considered professional career advice. Always consult with a qualified career advisor for personalized guidance.
