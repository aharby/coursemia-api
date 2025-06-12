<h2>Exam Submission Confirmation</h2>

<p>Dear {{ $data['user_name'] }},</p>

<p>Thank you for submitting your exam. We’ve successfully received your responses and are currently processing them.</p>

<hr>
<h4>📄 Exam Details</h4>
<ul>
    <li><strong>Course Title:</strong> {{ $data['course_title'] }}</li>
    <li><strong>Date Submitted:</strong> {{ $data['submitted_at'] }}</li>
    <li><strong>Total Questions:</strong> {{ $data['total_questions'] }}</li>
    <li><strong>Correct Answers:</strong> {{ $data['correct_answers'] }}</li>
</ul>


<h4>📝 Your Score</h4>
<p><strong>{{ $data['score_percentage'] }}%</strong> ({{ $data['correct_answers'] }} / {{ $data['total_questions'] }})</p>

<hr>

<p>If you have any questions or concerns about your submission, feel free to contact our support team.</p>

<p>Best regards,<br>
The Exam Team</p>

<p style="font-size: 0.8em; color: #999;">
    This is an automated message. Please do not reply directly to this email.
</p>
