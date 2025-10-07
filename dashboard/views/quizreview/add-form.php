<?php
 
$data=$formdata;
// Decode JSON data for easy access
$submit_data = json_decode($data['quiz_submit_data'], true);
$autoresult_data = json_decode($data['quiz_autoresult_data'], true);

 
?>
 
    <title>Quiz Results</title>
    <style>
        :root {
            --color-primary: #F5411F;
            --color-secondary: #14A0A3;
            --color-light: #f9f9f9;
            --color-dark: #333;
            --color-muted: #6c757d;
            --color-success: #28a745;
            --color-danger: #dc3545;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--color-light);
            margin: 0;
            padding: 0;
            color: var(--color-dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .facebox-panel {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .facebox-panel__head {
            background-color: var(--color-primary);
            color: #fff;
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .facebox-panel__body {
            padding: 20px;
            background-color: var(--color-light);
        }

        h3 {
            color: var(--color-primary);
            font-size: 20px;
            margin-bottom: 10px;
        }

        h4 {
            color: var(--color-secondary);
            font-size: 20px;
            margin-bottom: 10px;
        }

        .answer-item {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .answer-item p {
            margin: 8px 0;
            font-size: 16px;
        }

        .answer-item strong {
            color: var(--color-secondary);
        }

        .status-correct {
            color: var(--color-success);
            font-weight: bold;
        }

        .status-incorrect {
            color: var(--color-danger);
            font-weight: bold;
        }

        .result-summary {
            padding: 20px;
            background-color: #f4f4f9;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .result-summary p {
            margin: 8px 0;
        }

        .result-summary .score {
            font-weight: bold;
            font-size: 18px;
            color: var(--color-primary);
        }

        .result-summary .status {
            font-weight: bold;
            color: var(--color-secondary);
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            font-size: 16px;
            color: var(--color-dark);
            padding: 5px 0;
        }

        /* ul li::before {
            content: "•";
            color: var(--color-primary);
            margin-right: 8px;
        } */

        @media (max-width: 768px) {
            .facebox-panel {
                margin: 10px;
            }
            h3, h4 {
                font-size: 18px;
            }
        }

        <style>
    .grading-section {
        display: flex;
        align-items: center;
        margin-top: 10px;
        gap: 10px;
    }

    .grading-section label {
        font-weight: bold;
        color: var(--color-dark);
    }

    .grading-section input {
        width: 80px;
        padding: 5px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        font-size: 16px;
    }

    .grading-section .max-marks {
        color: var(--color-muted);
        font-size: 14px;
        font-weight: bold;
    }

    .submit-button {
        padding: 10px 20px;
        background-color: var(--color-primary);
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .submit-button:hover {
        background-color: var(--color-secondary);
    }

    @media (max-width: 768px) {
        .grading-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .grading-section input {
            width: 100%;
        }
    }
</style>

    </style>
</head>
<body>

<div class="container">
    <div class="facebox-panel">
        <div class="facebox-panel__head">
            Quiz Result : <?php
       
            echo $data['quiz_title']; ?>
        </div>
        <div class="facebox-panel__body">
            <!-- Result Summary -->
            <div class="result-summary">
                <p><strong>Course Name:</strong> <?php echo $data['course_title']; ?></p>
                <!-- <p><strong>Lecture ID:</strong> <?php echo $data['quiz_lecture_id']; ?></p> -->
                <p><strong>Attempt:</strong> <?php echo $data['attempt']; ?></p>
                <p class="score"><strong>Score:</strong> <?php echo $data['score']; ?> / <?php echo $data['total_marks']; ?></p>
                <p class="status"><strong>Status:</strong> <?php echo $data['status'] == 0 ? 'Pending' : 'Completed'; ?></p>
                <p><strong>Submitted On:</strong> <?php echo $data['quiz_added_on']; ?></p>
            </div>

            <!-- Submitted Answers -->
            <!-- <div class="answers-section">
                <h4>Submitted Answers:</h4>
                <ul>
                    <?php
                    //echo '<pre>';print_r($questionsDetails);die;
                    foreach ($questionsDetails as $detail) {
                        echo '<li class="answer-item">';
                        echo "<h3>Question: " . $detail['question_title'] . "</h3>";
                        if($detail['question_type']==1 || $detail['question_type']==2){
                        echo "<ul>";
                        foreach ($detail['options'] as $key => $option) {
                            echo "<li>" . $key . ". " . $option . "</li>";
                        }
                        echo "</ul>";
                   
                        echo "<p><strong>Correct Answer:</strong> " . $detail['correct_answer'] . "</p>";
                    }
                        echo "<p><strong>Submitted Answer:</strong> " . $detail['submitted_answer'] . "</p>";

                        if($detail['question_type']==1 || $detail['question_type']==2){
                        echo "<p><strong>Status:</strong> <span class='" . ($detail['is_correct'] ? 'status-correct' : 'status-incorrect') . "'>" . ($detail['is_correct'] ? "Correct" : "Incorrect") . "</span></p>";
                        }
                        if($detail['question_type']==3){

                        }
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div> -->






            <div class="answers-section">
    <h4>Submitted Answers:</h4>
    <!-- <form action="process_teacher_grades.php" method="POST"> -->
    <form id="grades-form">
        <input type="hidden" name="rade_id" value="<?php echo $data['id']; ?>">
        <input type="hidden" name="score" value="<?php echo $data['score']; ?>">
        <input type="hidden" name="course_id" value="<?php echo $data['course_id']; ?>">
        <input type="hidden" name="quizlectureid" value="<?php echo $data['quiz_lecture_id']; ?>">
        <input type="hidden" name="quiz_learner_id" value="<?php echo $data['quiz_learner_id']; ?>">
        <input type="hidden" name="quiz_pass_percentage" value="<?php echo $data['quiz_pass_percentage']; ?>">
        <input type="hidden" name="totalmarks" value="<?php echo $data['total_marks']; ?>">
        <ul>
            <?php
            //echo '<pre>';print_r($questionsDetails);die;
            foreach ($questionsDetails as $detail) {
                echo '<li class="answer-item">';
                echo "<h3>Question: " . $detail['question_title'] . "</h3>";
                
                // Display options for MCQs
                if ($detail['question_type'] == 1 || $detail['question_type'] == 2) {
                    echo "<ul>";
                    foreach ($detail['options'] as $key => $option) {
                        echo "<li>" . $key . ". " . $option . "</li>";
                    }
                    echo "</ul>";
                    echo "<p><strong>Correct Answer:</strong> " . $detail['correct_answer'] . "</p>";
                }
                
                echo "<p><strong>Submitted Answer:</strong> " . $detail['submitted_answer'] . "</p>";

                echo "<p><strong>Explanation:</strong> " . $detail['explanation'] . "</p>";
                
                // Status for MCQs
                if ($detail['question_type'] == 1 || $detail['question_type'] == 2 || $detail['question_type'] == 3 ) {
                    echo "<p><strong>Status:</strong> <span class='" . (($detail['status'] === 'correct') ? 'status-correct' : 'status-incorrect') . "'>" . (($detail['status'] === 'correct') ? "Correct" : "Incorrect") . "</span></p>";

                }
                
                // Add a text box for teacher grading for question_type=3
                // if ($detail['question_type'] == 3) {
                //     echo "<div class='grading-section'>";
                //     if($data['status']==0){   echo "<label for='grade_" . $detail['question_id'] . "'>Grade:</label>";
                //       echo "<input type='number' name='grades[" . $detail['question_id'] . "]' data-max-marks=".$detail['question_marks']."  id='grade_" . $detail['question_id'] . "' min='0' max='" . $detail['question_marks'] . "' required>";  
                //     echo "<span class='max-marks'> / " . $detail['question_marks'] . "</span>"; }
                //     echo "</div>";
                // }
                
                echo '</li>';
            }
            ?>
        </ul>
        <!-- Submit button -->
        <div>
            <!-- <?php  if($data['status']==0){ ?>
            <button type="button" id="submit-grades" onclick="addgrade()" class="submit-button">Submit Grades</button>
           <?php  } ?> -->
        </div>
    </form>
</div>


        </div>
    </div>
</div>
 
