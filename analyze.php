<?php


require __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;


$GEMINI_API_KEY = getenv('GEMINI_API_KEY') ?: 'AIzaSyBoAwr4yWfE-oz4_5kcYQuBAnCpvj055wM'; 

$GEMINI_MODEL   = 'gemini-2.5-flash'; 

// Helper: safely escape HTML
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Initialize variables for messages
$error = '';
$aiResult = '';
$resumeText = '';
$originalName = '';

// Handle only POST requests with a file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['resumeFile']) || $_FILES['resumeFile']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid resume file.';
    } else {
        $file = $_FILES['resumeFile'];

        // Basic validation: size & extension
        $allowedExt = ['pdf', 'doc', 'docx'];
        $maxSize    = 5 * 1024 * 1024; // 5 MB

        $originalName = $file['name'];
        $fileSize     = $file['size'];
        $tmpPath      = $file['tmp_name'];
        $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $error = 'Only PDF, DOC, and DOCX files are allowed.';
        } elseif ($fileSize > $maxSize) {
            $error = 'File is too large. Maximum allowed size is 5 MB.';
        } else {
            // Create uploads folder if missing
            $uploadDir = __DIR__ . '/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Create unique file path
            $newFileName = uniqid('resume_', true) . '.' . $ext;
            $destPath    = $uploadDir . '/' . $newFileName;

            if (!move_uploaded_file($tmpPath, $destPath)) {
                $error = 'Failed to save uploaded file.';
            } else {
                // 🔹 File uploaded successfully – now extract text based on type

                try {
                    if ($ext === 'pdf') {
                        // PDF → text using smalot/pdfparser
                        $parser = new PdfParser();
                        $pdf = $parser->parseFile($destPath);
                        $resumeText = $pdf->getText();

                    } elseif ($ext === 'docx') {
                        // DOCX → text using PhpOffice\PhpWord
                        $phpWord = WordIOFactory::load($destPath);
                        $resumeText = '';

                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                if (method_exists($element, 'getText')) {
                                    $resumeText .= $element->getText() . "\n";
                                }
                            }
                        }

                    } elseif ($ext === 'doc') {
                        // Simplest approach: ask user to convert to DOCX
                        $error = "Legacy .doc format is not supported for text extraction. Please convert the file to DOCX or PDF and upload again.";
                    }
                } catch (Exception $e) {
                    $error = 'Error while extracting text from the resume: ' . $e->getMessage();
                }

                // If we have no error and no text, something went wrong
                if (!$error && trim($resumeText) === '') {
                    $error = 'Could not extract text from your resume. Please check that your file is not scanned as an image and try again.';
                }

                // --- CALL GEMINI API (or fallback demo) ---
                if (!$error) {

                    // Build AI prompt using actual resume text
                 $prompt = "You are an AI resume reviewer. Analyze the following resume context "
    . "and give:\n"
    . "1. An overall score out of 10\n"
    . "2. A brief summary of strengths\n"
    . "3. A bullet list of improvement suggestions\n\n"
    . "Resume context:\n" . $resumeText;


                    if (file_exists($destPath)) {
        unlink($destPath);
    }


                    // If no real key set, use static demo output for assignment
                    if ($GEMINI_API_KEY === 'AIzaSyBoAwr4yWfE-oz4_5kcYQuBAnCpvj055wM') {
                        $aiResult = "
📄 Demo Resume Analysis (no live AI key configured)

⭐ Overall Resume Score: 8.3 / 10

✅ Strengths:
• Clear sectioning for Education, Skills, and Experience.
• Bullet points use good action verbs.
• Experience appears relevant to the target domain.
• Contact information is easy to find.

⚠️ Areas for Improvement:
• Add measurable achievements (numbers, %, counts) to show impact.
• Tailor your summary/profile more clearly to a specific role.
• Expand the skills section with both technical and soft skills.
• Ensure consistent date formatting across all experiences.

💡 Recommendation:
Customize this resume slightly for each job by mirroring key skills and keywords from the job description. This will improve both recruiter interest and ATS compatibility.";
                    } else {
                        // Real Gemini API call using v1 endpoint
                        $payload = [
                            'contents' => [[
                                'parts' => [[ 'text' => $prompt ]]
                            ]]
                        ];

                        $url = "https://generativelanguage.googleapis.com/v1/models/{$GEMINI_MODEL}:generateContent?key={$GEMINI_API_KEY}";

                        $ch = curl_init($url);
                        curl_setopt_array($ch, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST           => true,
                            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                            CURLOPT_POSTFIELDS     => json_encode($payload),
                            CURLOPT_TIMEOUT        => 30,
                        ]);

                        $response = curl_exec($ch);

                        if ($response === false) {
                            $error = 'Error contacting AI API: ' . curl_error($ch);
                        } else {
                            $data = json_decode($response, true);

                            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                                // Normal, successful response
                                $aiResult = $data['candidates'][0]['content']['parts'][0]['text'];

                            } elseif (isset($data['error']['message'])) {
                                // Gemini returned an error JSON
                                $error = 'AI error: ' . $data['error']['message'];

                            } else {
                                // Unknown format – show part of the raw response to debug
                                $error = 'Unexpected AI response: ' . substr(json_encode($data), 0, 300) . '...';
                            }
                        }

                        curl_close($ch);
                    }
                }
            }
        }
    }
} else {
    $error = 'Invalid request method. Please upload your resume from the main page.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume Analysis Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="header">
        <div><h1>AI Powered Resume Analyser</h1></div>
        <div><p>Check the industry readiness of your resume</p></div>
    </div>


    <div class="container">
        <h2>Analysis Result</h2>

        <?php if ($error): ?>
            <p style="color: #ff6b6b; font-weight: bold; margin-bottom: 1rem;">
                <?= h($error) ?>
            </p>
            <p>
                <a href="index.html" style="color:#888; text-decoration:underline;">← Go back and try again</a>
            </p>
        <?php else: ?>
            <p><strong>File uploaded successfully:</strong> <?= h($originalName) ?></p>
            <h3>AI Feedback</h3>
          <pre style="text-align:left; white-space:pre-wrap; background:#000; padding:1rem; border-radius:8px; max-height:400px; overflow:auto;">
<?= h($aiResult) ?>
</pre>

            <p>
                <a href="index.html" style="color:#888; text-decoration:underline;">← Analyze another resume</a>
            </p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <div id="suggestions">
            <a href="sample.html">Examplery Templates</a> |
        </div>
        <div id="recommendIdeas">
            <a href="suggestions.html">Recommended Ideas or Ask for more features</a>
        </div>
    </div>
    
</body>
</html>
