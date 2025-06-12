<h2>Exam Submission Confirmation</h2>

<p>Dear {{ $data['user_name'] }},</p>

<p>Thank you for submitting your exam. We’ve successfully received your responses and are currently processing them.</p>

<hr>
<h4>📄 Exam Details</h4>
<ul>
    <li><strong>Course Title</strong> : {{ $data['course_title'] }}</li>
    <li><strong>Submitted at</strong> : {{ $data['submitted_at'] }}</li>
    <li><strong>Total Questions</strong> : {{ $data['total_questions'] }}</li>
    <li><strong>Correct Answers</strong> : {{ $data['correct_answers'] }}</li>
    <li><strong>Passing Score</strong> : {{ $data['passing_score'] }}%</li>
</ul>


<h4>📝 Your Score</h4>
<p><strong>{{ $data['score_percentage'] }}%</strong> ({{ $data['correct_answers'] }}/{{ $data['total_questions'] }} correctedly answered)</p>
@php
    $passed = $data['score_percentage'] >= $data['passing_score'];
@endphp

@if($passed)
    <p style="color: green; font-weight: bold; font-size: 1.1em;">
        🎉 Congratulations! You have <u>passed</u> the exam. Great job!
    </p>
@else
    <p style="color: red; font-weight: bold; font-size: 1.1em;">
        😔 Unfortunately, you have <u>not passed</u> this time. Don’t give up—review your answers and try again!
    </p>
@endif

@if(isset($data['questions']) && is_array($data['questions']) && count($data['questions']) > 0)
    <h4>📋 Question Breakdown</h4>
    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>Question</th>
                <th>Status</th>
                <th>Your Answer</th>
                <th>Correct Answer</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['questions'] as $question)
                <tr>
                    <td>{{ $question['title'] }}</td>
                    @if($question['is_correctly_answered'])
                        <td style="color: green;">Correct</td>
                        <td>{{ $question['selected_answer'] }}</td>
                        <td>{{ $question['selected_answer'] }}</td>
                    @else
                        <td style="color: red;">Incorrect</td>
                        <td>{{ $question['selected_answer'] }}</td>
                        <td>{{ $question['correct_answer'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
<hr>

<p>If you have any questions or concerns about your submission, feel free to contact our support team.</p>

<p>Best regards,<br>
The Exam Team</p>

<p style="font-size: 0.8em; color: #999;">
    This is an automated message. Please do not reply directly to this email.
</p>
