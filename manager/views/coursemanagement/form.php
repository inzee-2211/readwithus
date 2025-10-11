<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
?>
<style>
    .rightalign {
    display: flex;
    justify-content: flex-end; /* Aligns the button to the right */
    margin-right: 100px;
}
/* CSS to hide the new sub-topic field by default */
.hidden {
    display: none;
}


    </style>
<section class="section">


 

    <div class="sectionhead d-flex justify-content-between align-items-center">
   
    <h4 class="mb-0"><?php echo Label::getLabel('LBL_ADD_QUIZ'); ?></h4>
 
    <a href="<?php echo CONF_WEBROOT_FRONT_URL . 'public/uploads/sample_csv/questions.csv'; ?>" 
       class="btn btn--primary btn--sm" 
       download 
       title="Download sample course import file">
        <i class="ion-android-download"></i> Download Sample CSV
    </a>
</div>


    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <!-- <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                         <?php
                        $inactive = ($categoryId == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                        ?>
                            <li class=" lang-li-js <?php echo $inactive; ?>">
                                <a href="javascript:void(0);" data-id="<?php echo $langId; ?>" <?php if ($categoryId > 0) { ?> onclick="langForm(<?php echo $categoryId; ?>, <?php echo $langId; ?>);" <?php } ?>>
                                    <?php echo $langName; ?>
                                </a>
                            </li>
                        <?php } ?>  
                    </ul> -->
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                          <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>






<script>

</script>







<script>
    var TYPE_FREE = "<?php echo Course::TYPE_FREE; ?>";
    var TYPE_PAID = "<?php echo Course::TYPE_PAID; ?>";



 


    

    document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        function handleDropdownChange(selectId, inputId) {
            var selectElement = document.getElementById(selectId);
            var inputElement = document.getElementById(inputId);

            if (selectElement) {
                selectElement.addEventListener("change", function () {
                    if (this.value === "add_new") {
                        inputElement.style.display = "block";
                        inputElement.focus();
                    } else {
                        inputElement.style.display = "none";
                    }
                });
            } else {
                console.error("Dropdown not found:", selectId);
            }
        }

        // Handle "Add New" Level and Topic fields
        handleDropdownChange("level", "new_level");
        handleDropdownChange("topic", "new_topic");
    }, 500); // Delay to ensure elements exist
});

function handleLevelChange(levelId) {
   
    var newLevelInput = document.getElementById("new_level");

    if (!newLevelInput) {
        console.error("Element with ID 'new_level' not found.");
        return; // Exit if element does not exist
    }
 
    if (levelId === "add_new") {
        newLevelInput.style.display = "block";
        newLevelInput.focus();
    } else {
        newLevelInput.style.display = "none";
       // document.getElementById('subject').style.display = 'none';
        document.getElementById('subject').innerHTML = ''; // Clear previous subjects
        
        // Prepare form data (you can add more parameters if needed)
        var formData = new FormData();
        formData.append('level_id', levelId);

        // Make AJAX request to fetch subjects based on selected level
        $.ajax({
                url: fcom.makeUrl('Coursemanagement', 'getsubjectsforlevel'), // Your controller method URL
                type: 'POST',
                data: { levelId: levelId }, // Pass the selected level ID
                dataType: 'json',
                success: function(response) {
        console.log('Response from server:', response); // Log the entire response object

        // First, check if the response status is success (status === 1)
        if (response.status === 1) {
           
            // Check if 'data' exists and is an object
            if (response && typeof response.data === 'object' && Object.keys(response.data).length > 0) {
                var subjects = response.data; // The subjects are in 'data' as an object

                // Clear the dropdown before appending new subjects
                $('#subject').empty();

                // Append the "Select Subject" option first (or any placeholder you want)
                $('#subject').append('<option readonly value="">Select Subject</option>');
                $('#subject').append('<option value="add_new">➕ Add New</option>');
                // Loop through the object to access each subject
                for (var id in subjects) {
                   
                    if (subjects.hasOwnProperty(id)) {
                        var subject = subjects[id];
 
                        // Check if the subject is the "Add New" option
                        if (id === 'add_new') {
                            console.log('Add New option detected:', subject);
                            
                        } else {
                            // Append regular subjects to the dropdown
                            $('#subject').append('<option value="' + id + '">' + subject + '</option>');
                        }
                    }
                }
                 
            } else {
              
                $('#subject').empty().append('<option value="">No subjects available</option>').append('<option value="add_new">➕ Add New</option>');
            }
        } else {
           
            $('#subject').empty().append('<option value="">No subjects available</option>').append('<option value="add_new">➕ Add New</option>');
        }
    },
    error: function(xhr, status, error) {
        console.log('AJAX Error:', error);
    }
        });



    }


   

}

document.addEventListener("DOMContentLoaded", function () {
    handleLevelChange(document.getElementById("topic")?.value || '');
});

function handleTierTypeChange() {
    var tierOther = document.getElementById('tier_other');
    var typeOther = document.getElementById('type_other');
    
    // Show the 'Other' text field for Tier
    if (document.querySelector('input[name="tier"]:checked').value === 'Other') {
        tierOther.style.display = 'block';
    } else {
        tierOther.style.display = 'none';
    }

    // Show the 'Other' text field for Type
    if (document.querySelector('input[name="type"]:checked').value === 'Other') {
        typeOther.style.display = 'block';
    } else {
        typeOther.style.display = 'none';
    }
}

function handleSubjectChange(selectedSubjectId) {
    var newSubjectInput = document.getElementById("new_subject");

    if (!newSubjectInput) {
        console.error("Element with ID 'new_subject' not found.");
        return; // Exit if element does not exist
    }

    if (selectedSubjectId === "add_new") {
        newSubjectInput.style.display = "block";
        newSubjectInput.focus();
    } else {
        newSubjectInput.style.display = "none";

 

        $.ajax({
            url: fcom.makeUrl('Coursemanagement', 'gettopicforsubject'),
            type: 'POST',
            data: { subjectId: selectedSubjectId }, // Send selected subject ID
            dataType: 'json',
            success: function(response) {
                console.log('Response from server:', response); // Log the entire response object
                
                // Clear the topic dropdown and add default options
                $('#topic').empty().append('<option value="">Select Topic</option>'); // Default option
                
                // Always add "Add New" option at the end of the topic dropdown
                $('#topic').append('<option value="add_new">➕ Add New</option>');
                
                // If topics are returned from the server, add them to the dropdown
                if (response.status === 1) {
                    if (response.data && typeof response.data === 'object' && Object.keys(response.data).length > 0) {
                        var topics = response.data;
                        
                        // Loop through topics and append them to the dropdown
                        for (var id in topics) {
                            if (topics.hasOwnProperty(id)) {
                                var topic = topics[id];
                                $('#topic').append('<option value="' + id + '">' + topic + '</option>');
                            }
                        }
                    }
                } else {
                    console.log('No topics found for the selected subject.');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
            }
        });

    }
}

function handleExamBoardChange(value) {
    const newExamBoardField = document.getElementById('new_examboard');
    if (value === 'add_new') {
        newExamBoardField.style.display = 'block';
    } else {
        newExamBoardField.style.display = 'none';
    }
}


function handleTierChange(value) {
    document.getElementById('new_tier').style.display = (value === 'add_new') ? 'block' : 'none';
}

function handleTypeChange(value) {
    document.getElementById('new_type').style.display = (value === 'add_new') ? 'block' : 'none';
}

function handleYearChange(value) {
    document.getElementById('new_year').style.display = (value === 'add_new') ? 'block' : 'none';
}


document.addEventListener("DOMContentLoaded", function () {
    handleSubjectChange(document.getElementById("subject")?.value || '');
});


document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("topic").addEventListener("change", function () {
        fetchSubtopics(this.value);
    });
});

function fetchSubtopics(topicId) {
    if (topicId === "add_new") {
        document.getElementById("new_topic").style.display = "block";
        return;
    } else {
        document.getElementById("new_topic").style.display = "none";
    }

    // Fetch subtopics from the backend using AJAX
    fetch(`/get-subtopics?topic=${topicId}`)
        .then(response => response.json())
        .then(data => {
            let subtopicSelect = createSubtopicDropdown(data);
            let subtopicContainer = document.getElementById("subtopic-container");
            subtopicContainer.innerHTML = ""; // Clear existing subtopics
            subtopicContainer.appendChild(subtopicSelect);
        })
        .catch(error => console.error("Error fetching subtopics:", error));
}

 
 
function addSubTopicFields() {
    let subtopicContainer = document.getElementById("subtopic-container");
    let subtopicSelect = document.querySelector(".subtopic-dropdown");

    if (subtopicSelect) {
        let clonedSelect = subtopicSelect.cloneNode(true);
        subtopicContainer.appendChild(clonedSelect);
    } 
    // else {
    //     alert("Please select a topic first.");
    // }
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btn_add_subtopic").addEventListener("click", addSubTopicFields);
});

  /*
function addSubTopicFields() {
    let container = document.getElementById("subtopic-container");

    let rowDiv = document.createElement("div");
    rowDiv.classList.add("row", "mt-2", "subtopic-section");

    let colDiv = document.createElement("div");
    colDiv.classList.add("col-md-12");

    let fieldSetDiv = document.createElement("div");
    fieldSetDiv.classList.add("field-set", "p-3", "border", "rounded");

 
    fieldSetDiv.appendChild(createFieldWrapper("Sub-Topic", "subtopics[]", "text"));
    fieldSetDiv.appendChild(createFieldWrapper("Video URL", "video_urls[]", "text"));

    fieldSetDiv.appendChild(createFieldWrapper("Upload Quiz CSV", "quiz_csvs[]", "file"));

     fieldSetDiv.appendChild(createFieldWrapper("Upload Past Exams", "past_exams[]", "file", [], true));

     let removeBtnDiv = document.createElement("div");
    removeBtnDiv.classList.add("d-flex", "justify-content-end", "mt-2", "rightalign");  

    let removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.innerText = "❌ Remove";
    removeBtn.classList.add("btn", "btn-danger", "btn-sm");
    removeBtn.onclick = function () {
        container.removeChild(rowDiv);
    };

    removeBtnDiv.appendChild(removeBtn); 
    fieldSetDiv.appendChild(removeBtnDiv);  

    colDiv.appendChild(fieldSetDiv);
    rowDiv.appendChild(colDiv);
    container.appendChild(rowDiv);
}
 
  
 */
function createFieldWrapper(labelText, name, type, options = [], multiple = false) {
    let fieldSet = document.createElement("div");
    fieldSet.classList.add("field-set");

    let captionWrapper = document.createElement("div");
    captionWrapper.classList.add("caption-wraper");

    let label = document.createElement("label");
    label.classList.add("field_label");
    label.innerText = labelText;
    captionWrapper.appendChild(label);

    let fieldWrapper = document.createElement("div");
    fieldWrapper.classList.add("field-wraper");

    let fieldCover = document.createElement("div");
    fieldCover.classList.add("field_cover");

    let input;
    if (type === "select") {
        input = document.createElement("select");
        input.classList.add("form-control");
        input.name = name;

        options.forEach(optionData => {
            let option = document.createElement("option");
            option.value = optionData.value;
            option.innerText = optionData.text;
            input.appendChild(option);
        });
    } else {
        input = document.createElement("input");
        input.type = type;
        input.name = name;
        input.classList.add("form-control");
        if (multiple) input.multiple = true;
    }

    fieldCover.appendChild(input);
    fieldWrapper.appendChild(fieldCover);
    fieldSet.appendChild(captionWrapper);
    fieldSet.appendChild(fieldWrapper);

    return fieldSet;
}


/*
function addSubTopicFields() {
    let container = document.getElementById("subtopic-container");

    let rowDiv = document.createElement("div");
    rowDiv.classList.add("row", "mt-2", "subtopic-section");

    let colDiv = document.createElement("div");
    colDiv.classList.add("col-md-12");

    let fieldSetDiv = document.createElement("div");
    fieldSetDiv.classList.add("field-set", "p-3", "border", "rounded");

    // Create the Sub-Topic Dropdown field
    let subTopicSelectWrapper = document.createElement("div");
    subTopicSelectWrapper.classList.add("field-set");

    let captionWrapper = document.createElement("div");
    captionWrapper.classList.add("caption-wraper");
    let label = document.createElement("label");
    label.classList.add("field_label");
    label.innerText = "Sub-Topic";
    captionWrapper.appendChild(label);
    subTopicSelectWrapper.appendChild(captionWrapper);

    let subTopicSelectWrapperInner = document.createElement("div");
    subTopicSelectWrapperInner.classList.add("field-wraper");

    let subTopicSelectCover = document.createElement("div");
    subTopicSelectCover.classList.add("field_cover");

    let subTopicSelect = document.createElement("select");
    subTopicSelect.classList.add("form-control");
    subTopicSelect.name = "subtopics[]";
    subTopicSelect.id = `subtopic_${Date.now()}`;

    let option1 = document.createElement("option");
    option1.value = "";
    option1.innerText = "Select Sub-Topic";

    let option2 = document.createElement("option");
    option2.value = "add_new";
    option2.innerText = "Add New";

    subTopicSelect.appendChild(option1);
    subTopicSelect.appendChild(option2);

    subTopicSelectCover.appendChild(subTopicSelect);
    subTopicSelectWrapperInner.appendChild(subTopicSelectCover);
    subTopicSelectWrapper.appendChild(subTopicSelectWrapperInner);

    // Add the Sub-Topic Select field to the fieldset
    fieldSetDiv.appendChild(subTopicSelectWrapper);

    // Create the input field for the new sub-topic (initially hidden)
    let newSubTopicWrapper = document.createElement("div");
    newSubTopicWrapper.classList.add("field-set", "hidden"); // Initially hidden using the 'hidden' class

    let newCaptionWrapper = document.createElement("div");
    newCaptionWrapper.classList.add("caption-wraper");
    let newLabel = document.createElement("label");
    newLabel.classList.add("field_label");
    newLabel.innerText = "New Sub-Topic";
    newCaptionWrapper.appendChild(newLabel);
    newSubTopicWrapper.appendChild(newCaptionWrapper);

    let newFieldWrapper = document.createElement("div");
    newFieldWrapper.classList.add("field-wraper");

    let newFieldCover = document.createElement("div");
    newFieldCover.classList.add("field_cover");

    let newSubTopicInput = document.createElement("input");
    newSubTopicInput.classList.add("form-control");
    newSubTopicInput.name = "new_subtopics[]";
    newSubTopicInput.type = "text";
    newSubTopicInput.placeholder = "Enter new sub-topic";

    newFieldCover.appendChild(newSubTopicInput);
    newFieldWrapper.appendChild(newFieldCover);
    newSubTopicWrapper.appendChild(newFieldWrapper);

    // Add the New Sub-Topic input field to the fieldset
    fieldSetDiv.appendChild(newSubTopicWrapper);

    // Show the "New Sub-Topic" input when "Add New" is selected
    subTopicSelect.addEventListener("change", function() {
        if (subTopicSelect.value === "add_new") {
            newSubTopicWrapper.classList.remove("hidden"); // Show input field
        } else {
            newSubTopicWrapper.classList.add("hidden"); // Hide input field
        }
    });

    // Add Video URL, Quiz CSV, and Past Exams fields
    fieldSetDiv.appendChild(createFieldWrapper1("Video URL", "video_urls[]", "text"));
    fieldSetDiv.appendChild(createFieldWrapper1("Upload Quiz CSV", "quiz_csvs[]", "file"));
    fieldSetDiv.appendChild(createFieldWrapper1("Upload Past Exams", "past_exams[]", "file", [], true));

    // Add the "Remove" button
    let removeBtnDiv = document.createElement("div");
    removeBtnDiv.classList.add("d-flex", "justify-content-end", "mt-2", "rightalign");

    let removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.innerText = "❌ Remove";
    removeBtn.classList.add("btn", "btn-danger", "btn-sm");
    removeBtn.onclick = function () {
        container.removeChild(rowDiv);
    };

    removeBtnDiv.appendChild(removeBtn);
    fieldSetDiv.appendChild(removeBtnDiv);

    colDiv.appendChild(fieldSetDiv);
    rowDiv.appendChild(colDiv);
    container.appendChild(rowDiv);
}

// Helper function to create fields (for Video URL, Quiz CSV, and Past Exams)
function createFieldWrapper1(labelText, name, type, additionalAttributes = [], isFile = false) {
    let wrapperDiv = document.createElement("div");
    wrapperDiv.classList.add("field-set");

    // Caption wrapper with label
    let captionWrapper = document.createElement("div");
    captionWrapper.classList.add("caption-wraper");
    let label = document.createElement("label");
    label.classList.add("field_label");
    label.innerText = labelText;
    captionWrapper.appendChild(label);
    wrapperDiv.appendChild(captionWrapper);

    // Field wrapper for input element
    let fieldWrapper = document.createElement("div");
    fieldWrapper.classList.add("field-wraper");

    // Cover the field
    let fieldCover = document.createElement("div");
    fieldCover.classList.add("field_cover");

    let input = document.createElement("input");
    input.type = type;
    input.name = name;
    input.classList.add("form-control");

    // If it's a file input, allow multiple files
    if (isFile) {
        input.setAttribute("multiple", "");
    }

    // Add additional attributes like placeholders for text inputs
    additionalAttributes.forEach(attr => {
        input.setAttribute(attr.name, attr.value);
    });

    fieldCover.appendChild(input);
    fieldWrapper.appendChild(fieldCover);
    wrapperDiv.appendChild(fieldWrapper);

    return wrapperDiv;
}

*/


// function handleTopicChange(value) {
//     var newLevelInput = document.getElementById("new_topic");

//     if (!newLevelInput) {
//         console.error("Element with ID 'new_topic' not found.");
//         return; // Exit if element does not exist
//     }

//     if (value === "add_new") {
//         newLevelInput.style.display = "block";
//         newLevelInput.focus();

//         addSubTopicFields(true);

        
//     } else {
//         newLevelInput.style.display = "none";
//         let subTopicSelect = document.getElementById("subtopic");
//         fetchSubTopics(value,subTopicSelect);
        
//     }
// }

// function fetchSubTopics(topicId,subTopicSelect) {
     
//      $.ajax({
//         url: fcom.makeUrl('Coursemanagement', 'getsubtopicsbytopic'),  // Update this URL to your correct endpoint
//         type: 'POST',
//         data: { topicId: topicId },
//         dataType: 'json',
//         success: function(response) {
//             if (response.status === 1) {
//                 // Clear previous sub-topic options
//                 subTopicSelect.innerHTML = '<option value="">Select Sub-Topic</option>';
                
//                 // Populate sub-topics returned from the server
//                 response.data.forEach(function(subtopic) {
//                     let option = document.createElement("option");
//                     option.value = subtopic.id;
//                     option.innerText = subtopic.name;
//                     subTopicSelect.appendChild(option);
//                 });

//                 // Always add "Add New" option at the end
//                 let optionAddNew = document.createElement("option");
//                 optionAddNew.value = "add_new";
//                 optionAddNew.innerText = "Add New";
//                 subTopicSelect.appendChild(optionAddNew);
//             } else {
//                 // Handle the case where no sub-topics are found
//                 console.log('No sub-topics found for the selected topic.');
//             }
//         },
//         error: function(xhr, status, error) {
//             console.log("Error fetching sub-topics: ", error);
//         }
//     });
// }

 


// document.addEventListener("DOMContentLoaded", function () {
//     handleTopicChange(document.getElementById("topic")?.value || '');
// });



function handleTopicChange(value) {
    var newLevelInput = document.getElementById("new_topic");

    if (!newLevelInput) {
        console.error("Element with ID 'new_topic' not found.");
        return;
    }

    if (value === "add_new") {
        newLevelInput.style.display = "block";
        newLevelInput.focus();
        addSubTopicFields();  // Add sub-topic fields dynamically
    } else {
        newLevelInput.style.display = "none";

        // Fetch sub-topics for the dynamically created sub-topic select elements
        let subTopicSelects = document.querySelectorAll("select[name='subtopics[]']");
        subTopicSelects.forEach(function(subTopicSelect) {
            fetchSubTopics(value, subTopicSelect);  // Fetch sub-topics for each select element
        });
    }
}

function fetchSubTopics(topicId, subTopicSelect) {

    $.ajax({
        url: fcom.makeUrl('Coursemanagement', 'getsubtopicsbytopic'),  // Update this URL to your correct endpoint
        type: 'POST',
        data: { topicId: topicId },
        dataType: 'json',
        success: function(response) {
    // Clear previous sub-topic options
    subTopicSelect.innerHTML = '<option value="">Select Sub-Topic</option>';

    // Check if response.data is an object and has properties
    if (response.data && typeof response.data === 'object' && Object.keys(response.data).length > 0) {
        var topics = response.data;

        // Iterate over the object keys and create options
        Object.keys(topics).forEach(function(key) {
            let option = document.createElement("option");
            option.value = key; // Use the key (numeric value) as the option value
            option.innerText = topics[key]; // Use the value (topic name) as the option text
            subTopicSelect.appendChild(option);
        });
    }

    // Always add "Add New" option at the end (if no subtopics were found)
    let optionAddNew = document.createElement("option");
    optionAddNew.value = "add_new";
    optionAddNew.innerText = "Add New";
    subTopicSelect.appendChild(optionAddNew);
},

        error: function(xhr, status, error) {
            console.log("Error fetching sub-topics: ", error);
        }
    });
}

function addSubTopicFields() {
    let container = document.getElementById("subtopic-container");

    if (!container) {
        console.error("Container for sub-topic is not found.");
        return;
    }

    // Create the sub-topic select element dynamically
    let subTopicSelect = document.createElement("select");
    subTopicSelect.classList.add("form-control");
    subTopicSelect.name = "subtopics[]";  // Ensuring it has the correct name attribute
    subTopicSelect.id = "subtopic_" + Date.now();  // Dynamic ID based on the current time

    // Add options to the select element
    let option1 = document.createElement("option");
    option1.value = "";
    option1.innerText = "Select Sub-Topic";

    let option2 = document.createElement("option");
    option2.value = "add_new";
    option2.innerText = "Add New";

    subTopicSelect.appendChild(option1);
    subTopicSelect.appendChild(option2);

    // Add the sub-topic select element to the container
    container.appendChild(subTopicSelect);

    // Add event listener to handle sub-topic selection for the new select element
    subTopicSelect.addEventListener("change", function() {
        if (subTopicSelect.value === "add_new") {
            showNewSubTopicField();
        } else {
            hideNewSubTopicField();
        }
    });

    // After adding the sub-topic select, trigger fetching sub-topics if a topic is already selected
    let topicSelect = document.getElementById("topic");
    if (topicSelect && topicSelect.value !== "") {
        fetchSubTopics(topicSelect.value, subTopicSelect);  // Fetch sub-topics for the new select element
    }
}

function showNewSubTopicField() {
    let newSubTopicWrapper = document.getElementById("new-subtopic-wrapper");
    if (newSubTopicWrapper) {
        newSubTopicWrapper.style.display = "block";
    }
}

function hideNewSubTopicField() {
    let newSubTopicWrapper = document.getElementById("new-subtopic-wrapper");
    if (newSubTopicWrapper) {
        newSubTopicWrapper.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // Initialize on load based on selected topic
    let topicSelect = document.getElementById("topic");
    handleTopicChange(topicSelect?.value || '');
});



function addSubTopicFields() {
    let container = document.getElementById("subtopic-container");

    let rowDiv = document.createElement("div");
    rowDiv.classList.add("row", "mt-2", "subtopic-section");

    let colDiv = document.createElement("div");
    colDiv.classList.add("col-md-12");

    let fieldSetDiv = document.createElement("div");
    fieldSetDiv.classList.add("field-set", "p-3", "border", "rounded");

    // Create the Sub-Topic Dropdown field
    let subTopicSelectWrapper = document.createElement("div");
    subTopicSelectWrapper.classList.add("field-set");

    let captionWrapper = document.createElement("div");
    captionWrapper.classList.add("caption-wraper");
    let label = document.createElement("label");
    label.classList.add("field_label");
    label.innerText = "Sub-Topic";
    captionWrapper.appendChild(label);
    subTopicSelectWrapper.appendChild(captionWrapper);

    let subTopicSelectWrapperInner = document.createElement("div");
    subTopicSelectWrapperInner.classList.add("field-wraper");

    let subTopicSelectCover = document.createElement("div");
    subTopicSelectCover.classList.add("field_cover");

    let subTopicSelect = document.createElement("select");
    subTopicSelect.classList.add("form-control");
    subTopicSelect.name = "subtopics[]";
    subTopicSelect.id = `subtopic_${Date.now()}`;

    let option1 = document.createElement("option");
    option1.value = "";
    option1.innerText = "Select Sub-Topic";

    let option2 = document.createElement("option");
    option2.value = "add_new";
    option2.innerText = "Add New";

    subTopicSelect.appendChild(option1);
    subTopicSelect.appendChild(option2);

    subTopicSelectCover.appendChild(subTopicSelect);
    subTopicSelectWrapperInner.appendChild(subTopicSelectCover);
    subTopicSelectWrapper.appendChild(subTopicSelectWrapperInner);

    // Add the Sub-Topic Select field to the fieldset
    fieldSetDiv.appendChild(subTopicSelectWrapper);

    // Create the input field for the new sub-topic (initially hidden)
    let newSubTopicWrapper = document.createElement("div");
    newSubTopicWrapper.classList.add("field-set", "hidden"); // Initially hidden using the 'hidden' class

    let newCaptionWrapper = document.createElement("div");
    newCaptionWrapper.classList.add("caption-wraper");
    let newLabel = document.createElement("label");
    newLabel.classList.add("field_label");
    newLabel.innerText = "New Sub-Topic";
    newCaptionWrapper.appendChild(newLabel);
    newSubTopicWrapper.appendChild(newCaptionWrapper);

    let newFieldWrapper = document.createElement("div");
    newFieldWrapper.classList.add("field-wraper");

    let newFieldCover = document.createElement("div");
    newFieldCover.classList.add("field_cover");

    let newSubTopicInput = document.createElement("input");
    newSubTopicInput.classList.add("form-control");
    newSubTopicInput.name = "new_subtopics[]";
    newSubTopicInput.type = "text";
    newSubTopicInput.placeholder = "Enter new sub-topic";

    newFieldCover.appendChild(newSubTopicInput);
    newFieldWrapper.appendChild(newFieldCover);
    newSubTopicWrapper.appendChild(newFieldWrapper);

    // Add the New Sub-Topic input field to the fieldset
    fieldSetDiv.appendChild(newSubTopicWrapper);

    // Show the "New Sub-Topic" input when "Add New" is selected
    subTopicSelect.addEventListener("change", function() {
        if (subTopicSelect.value === "add_new") {
            newSubTopicWrapper.classList.remove("hidden"); // Show input field
        } else {
            newSubTopicWrapper.classList.add("hidden"); // Hide input field
        }
    });

    // Dynamically populate the Sub-Topics based on the selected Topic
    subTopicSelect.addEventListener("change", function() {
        var selectedTopicId = subTopicSelect.value;
        if (selectedTopicId !== "add_new" && selectedTopicId !== "") {
            // Fetch sub-topics using AJAX based on selected Topic ID
            $.ajax({
                url: fcom.makeUrl('Coursemanagement', 'getsubtopicsbytopic'),  // Your URL here
                type: 'POST',
                data: { topicId: selectedTopicId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 1) {
                        // Clear the sub-topic options
                        subTopicSelect.empty();
                        subTopicSelect.append('<option value="">Select Sub-Topic</option>');
                        
                        // Populate sub-topics returned from the server
                        response.data.forEach(function(subtopic) {
                            let option = document.createElement("option");
                            option.value = subtopic.id;
                            option.innerText = subtopic.name;
                            subTopicSelect.appendChild(option);
                        });
                        
                        // Always add "Add New" option
                        let optionAddNew = document.createElement("option");
                        optionAddNew.value = "add_new";
                        optionAddNew.innerText = "Add New";
                        subTopicSelect.appendChild(optionAddNew);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching sub-topics: ", error);
                }
            });
        }
    });

    // Add Video URL, Quiz CSV, and Past Exams fields
    fieldSetDiv.appendChild(createFieldWrapper1("Video URL", "video_urls[]", "text"));
    fieldSetDiv.appendChild(createFieldWrapper1("Upload Quiz CSV", "quiz_csvs[]", "file"));
    fieldSetDiv.appendChild(createFieldWrapper1("Upload Past Exams", "past_exams[]", "file", [], true));

    // Add the "Remove" button
    let removeBtnDiv = document.createElement("div");
    removeBtnDiv.classList.add("d-flex", "justify-content-end", "mt-2", "rightalign");

    let removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.innerText = "❌ Remove";
    removeBtn.classList.add("btn", "btn-danger", "btn-sm");
    removeBtn.onclick = function () {
        container.removeChild(rowDiv);
    };

    removeBtnDiv.appendChild(removeBtn);
    fieldSetDiv.appendChild(removeBtnDiv);

    colDiv.appendChild(fieldSetDiv);
    rowDiv.appendChild(colDiv);
    container.appendChild(rowDiv);
}

// Helper function to create fields (for Video URL, Quiz CSV, and Past Exams)
function createFieldWrapper1(labelText, name, type, additionalAttributes = [], isFile = false) {
    let wrapperDiv = document.createElement("div");
    wrapperDiv.classList.add("field-set");

    // Caption wrapper with label
    let captionWrapper = document.createElement("div");
    captionWrapper.classList.add("caption-wraper");
    let label = document.createElement("label");
    label.classList.add("field_label");
    label.innerText = labelText;
    captionWrapper.appendChild(label);
    wrapperDiv.appendChild(captionWrapper);

    // Field wrapper for input element
    let fieldWrapper = document.createElement("div");
    fieldWrapper.classList.add("field-wraper");

    // Cover the field
    let fieldCover = document.createElement("div");
    fieldCover.classList.add("field_cover");

    let input = document.createElement("input");
    input.type = type;
    input.name = name;
    input.classList.add("form-control");

    // If it's a file input, allow multiple files
    if (isFile) {
        input.setAttribute("multiple", "");
    }

    // Add additional attributes like placeholders for text inputs
    additionalAttributes.forEach(attr => {
        input.setAttribute(attr.name, attr.value);
    });

    fieldCover.appendChild(input);
    
    fieldWrapper.appendChild(fieldCover);
    wrapperDiv.appendChild(fieldWrapper);

    return wrapperDiv;
}


</script>
 

